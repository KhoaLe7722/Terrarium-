[CmdletBinding()]
param(
    [string]$XamppRoot = 'C:\xampp',
    [string]$Database = 'terrarium_db',
    [string]$Username = 'root',
    [string]$Password = '',
    [string]$OutputRoot = '',
    [switch]$SkipUsers
)

$ErrorActionPreference = 'Stop'

function New-AbsolutePath {
    param([Parameter(Mandatory = $true)][string]$Path)

    if ([System.IO.Path]::IsPathRooted($Path)) {
        return [System.IO.Path]::GetFullPath($Path)
    }

    return [System.IO.Path]::GetFullPath((Join-Path (Get-Location) $Path))
}

function Invoke-MySqlQuery {
    param([Parameter(Mandatory = $true)][string]$Query)

    $args = @('--batch', '--raw', '--skip-column-names', '-u', $Username)
    if ($Password -ne '') {
        $args += "-p$Password"
    }
    $args += @('-e', $Query)

    $result = & $script:mysqlExe @args 2>&1
    if ($LASTEXITCODE -ne 0) {
        throw "mysql query failed: $($result -join [Environment]::NewLine)"
    }

    return @($result)
}

function Escape-SqlLiteral {
    param([Parameter(Mandatory = $true)][string]$Value)

    return $Value.Replace("'", "''")
}

$mysqlBin = Join-Path $XamppRoot 'mysql\bin'
$mysqldumpExe = Join-Path $mysqlBin 'mysqldump.exe'
$script:mysqlExe = Join-Path $mysqlBin 'mysql.exe'
$mysqlAdminExe = Join-Path $mysqlBin 'mysqladmin.exe'

if ([string]::IsNullOrWhiteSpace($OutputRoot)) {
    $OutputRoot = Join-Path ((Split-Path (Split-Path $PSCommandPath -Parent) -Parent)) 'backups\mysql'
}

foreach ($requiredPath in @($mysqldumpExe, $script:mysqlExe, $mysqlAdminExe)) {
    if (-not (Test-Path $requiredPath)) {
        throw "Missing required file: $requiredPath"
    }
}

$OutputRoot = New-AbsolutePath -Path $OutputRoot
$timestamp = Get-Date -Format 'yyyyMMdd-HHmmss'
$backupDir = Join-Path $OutputRoot $timestamp
$null = New-Item -ItemType Directory -Force -Path $backupDir

$pingArgs = @('--connect-timeout=5', 'ping', '-u', $Username)
if ($Password -ne '') {
    $pingArgs += "-p$Password"
}

$pingOutput = & $mysqlAdminExe @pingArgs 2>&1
if ($LASTEXITCODE -ne 0) {
    throw "MySQL is not responding: $($pingOutput -join [Environment]::NewLine)"
}

$databaseCheck = Invoke-MySqlQuery -Query "SHOW DATABASES LIKE '$Database';"
if ($databaseCheck -notcontains $Database) {
    throw "Database '$Database' was not found."
}

$databaseDumpFile = Join-Path $backupDir "$Database.sql"
$dumpArgs = @(
    '--default-character-set=utf8mb4',
    '--single-transaction',
    '--routines',
    '--triggers',
    '--events',
    '--hex-blob',
    '--databases',
    $Database,
    '-u',
    $Username,
    "--result-file=$databaseDumpFile"
)
if ($Password -ne '') {
    $dumpArgs += "-p$Password"
}

$dumpOutput = & $mysqldumpExe @dumpArgs 2>&1
if ($LASTEXITCODE -ne 0) {
    throw "mysqldump failed: $($dumpOutput -join [Environment]::NewLine)"
}

$usersDumpFile = Join-Path $backupDir 'mysql-users.sql'
if (-not $SkipUsers) {
    $grantLines = @(
        "-- Exported $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')",
        'SET SQL_NOTES=0;',
        ''
    )

    $userRows = Invoke-MySqlQuery -Query "SELECT CONCAT(User, CHAR(9), Host) FROM mysql.global_priv ORDER BY User, Host;"
    foreach ($row in $userRows) {
        if ([string]::IsNullOrWhiteSpace($row)) {
            continue
        }

        $parts = $row -split "`t", 2
        if ($parts.Count -ne 2) {
            continue
        }

        $user = Escape-SqlLiteral -Value $parts[0]
        $grantHost = Escape-SqlLiteral -Value $parts[1]

        $showCreateRows = Invoke-MySqlQuery -Query "SHOW CREATE USER '$user'@'$grantHost';"
        foreach ($showCreateRow in $showCreateRows) {
            $createParts = $showCreateRow -split "`t", 2
            if ($createParts.Count -eq 2) {
                $statement = $createParts[1].Trim()
            } else {
                $statement = $showCreateRow.Trim()
            }

            if ($statement -match '^CREATE USER ') {
                $statement = $statement -replace '^CREATE USER ', 'CREATE USER IF NOT EXISTS '
            }
            if ($statement -and $statement -notmatch ';$') {
                $statement += ';'
            }
            if ($statement) {
                $grantLines += $statement
            }
        }

        $grantRows = Invoke-MySqlQuery -Query "SHOW GRANTS FOR '$user'@'$grantHost';"
        foreach ($grantRow in $grantRows) {
            $statement = $grantRow.Trim()
            if ($statement -and $statement -notmatch ';$') {
                $statement += ';'
            }
            if ($statement) {
                $grantLines += $statement
            }
        }

        $grantLines += ''
    }

    Set-Content -Path $usersDumpFile -Value $grantLines -Encoding Ascii
}

$infoFile = Join-Path $backupDir 'backup-info.txt'
$infoLines = @(
    "created_at=$(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')",
    "xampp_root=$XamppRoot",
    "database=$Database",
    "username=$Username",
    "database_dump=$databaseDumpFile",
    "users_dump=$usersDumpFile",
    '',
    'restore_command=powershell -ExecutionPolicy Bypass -File .\scripts\mysql-restore.ps1 -BackupDir <path>'
)
Set-Content -Path $infoFile -Value $infoLines -Encoding Ascii

Write-Host "Backup created at $backupDir"
Write-Host "Database dump: $databaseDumpFile"
if (-not $SkipUsers) {
    Write-Host "Users dump: $usersDumpFile"
}
