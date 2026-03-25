[CmdletBinding(SupportsShouldProcess = $true)]
param(
    [Parameter(Mandatory = $true)]
    [string]$BackupDir,
    [string]$XamppRoot = 'C:\xampp',
    [string]$Username = 'root',
    [string]$Password = '',
    [string]$Database = 'terrarium_db',
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

function Invoke-MysqlFile {
    param(
        [Parameter(Mandatory = $true)][string]$Label,
        [Parameter(Mandatory = $true)][string]$FilePath
    )

    if (-not (Test-Path $FilePath)) {
        throw "Missing SQL file: $FilePath"
    }

    if (-not $PSCmdlet.ShouldProcess($FilePath, "Import $Label")) {
        return
    }

    $argsText = @('--default-character-set=utf8mb4', '-u', $Username)
    if ($Password -ne '') {
        $argsText += "-p$Password"
    }

    $quotedExe = '"' + $script:mysqlExe + '"'
    $quotedFile = '"' + $FilePath + '"'
    $commandLine = "$quotedExe $($argsText -join ' ') < $quotedFile"

    cmd /c $commandLine
    if ($LASTEXITCODE -ne 0) {
        throw "mysql import failed for $Label."
    }
}

$BackupDir = New-AbsolutePath -Path $BackupDir
$databaseDumpFile = Join-Path $BackupDir "$Database.sql"
$usersDumpFile = Join-Path $BackupDir 'mysql-users.sql'

$mysqlBin = Join-Path $XamppRoot 'mysql\bin'
$script:mysqlExe = Join-Path $mysqlBin 'mysql.exe'
$mysqlAdminExe = Join-Path $mysqlBin 'mysqladmin.exe'

foreach ($requiredPath in @($script:mysqlExe, $mysqlAdminExe)) {
    if (-not (Test-Path $requiredPath)) {
        throw "Missing required file: $requiredPath"
    }
}

$pingArgs = @('--connect-timeout=5', 'ping', '-u', $Username)
if ($Password -ne '') {
    $pingArgs += "-p$Password"
}

$pingOutput = & $mysqlAdminExe @pingArgs 2>&1
if ($LASTEXITCODE -ne 0) {
    throw "MySQL is not responding: $($pingOutput -join [Environment]::NewLine)"
}

Invoke-MysqlFile -Label "database $Database" -FilePath $databaseDumpFile

if ((-not $SkipUsers) -and (Test-Path $usersDumpFile)) {
    Invoke-MysqlFile -Label 'MySQL users and grants' -FilePath $usersDumpFile
}

Write-Host "Restore completed from $BackupDir"
