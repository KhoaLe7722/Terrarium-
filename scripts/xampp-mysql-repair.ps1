[CmdletBinding(SupportsShouldProcess = $true)]
param(
    [string]$XamppRoot = 'C:\xampp',
    [string]$Database = 'terrarium_db',
    [string]$BackupRoot = '',
    [switch]$StartAfterRepair
)

$ErrorActionPreference = 'Stop'

function New-AbsolutePath {
    param([Parameter(Mandatory = $true)][string]$Path)

    if ([System.IO.Path]::IsPathRooted($Path)) {
        return [System.IO.Path]::GetFullPath($Path)
    }

    return [System.IO.Path]::GetFullPath((Join-Path (Get-Location) $Path))
}

$mysqlDataRoot = Join-Path $XamppRoot 'mysql\data'
$mysqlSystemData = Join-Path $mysqlDataRoot 'mysql'
$mysqlSystemBackup = Join-Path $XamppRoot 'mysql\backup\mysql'
$mysqldExe = Join-Path $XamppRoot 'mysql\bin\mysqld.exe'

if ([string]::IsNullOrWhiteSpace($BackupRoot)) {
    $BackupRoot = Join-Path ((Split-Path (Split-Path $PSCommandPath -Parent) -Parent)) 'backups\mysql-system'
}

$BackupRoot = New-AbsolutePath -Path $BackupRoot

foreach ($requiredPath in @($mysqlSystemData, $mysqlSystemBackup, $mysqldExe)) {
    if (-not (Test-Path $requiredPath)) {
        throw "Missing required path: $requiredPath"
    }
}

$timestamp = Get-Date -Format 'yyyyMMdd-HHmmss'
$snapshotDir = Join-Path $BackupRoot $timestamp
$null = New-Item -ItemType Directory -Force -Path $snapshotDir

$mysqldProcess = Get-Process mysqld -ErrorAction SilentlyContinue
if ($mysqldProcess -and $PSCmdlet.ShouldProcess('mysqld.exe', 'Stop MySQL process')) {
    $mysqldProcess | Stop-Process -Force
}

$backupTargets = @(
    @{ Source = $mysqlSystemData; Destination = Join-Path $snapshotDir 'mysql' },
    @{ Source = (Join-Path $mysqlDataRoot $Database); Destination = Join-Path $snapshotDir $Database },
    @{ Source = (Join-Path $mysqlDataRoot 'mysql_error.log'); Destination = Join-Path $snapshotDir 'mysql_error.log' },
    @{ Source = (Join-Path $mysqlDataRoot 'mysqld.dmp'); Destination = Join-Path $snapshotDir 'mysqld.dmp' }
)

foreach ($target in $backupTargets) {
    if (Test-Path $target.Source) {
        if ($PSCmdlet.ShouldProcess($target.Source, "Copy to $($target.Destination)")) {
            Copy-Item -Recurse -Force $target.Source $target.Destination
        }
    }
}

Get-ChildItem $mysqlDataRoot -Filter 'aria_log*' -ErrorAction SilentlyContinue | ForEach-Object {
    $destination = Join-Path $snapshotDir $_.Name
    if ($PSCmdlet.ShouldProcess($_.FullName, "Copy to $destination")) {
        Copy-Item -Force $_.FullName $destination
    }
}

if ($PSCmdlet.ShouldProcess($mysqlSystemData, 'Replace corrupted MySQL system tables with XAMPP defaults')) {
    Remove-Item -Recurse -Force $mysqlSystemData
    Copy-Item -Recurse -Force $mysqlSystemBackup $mysqlSystemData
}

Get-ChildItem $mysqlDataRoot -Filter 'aria_log*' -ErrorAction SilentlyContinue | ForEach-Object {
    if ($PSCmdlet.ShouldProcess($_.FullName, 'Delete old Aria log file')) {
        Remove-Item -Force $_.FullName
    }
}

$mysqlPidFile = Join-Path $mysqlDataRoot 'mysql.pid'
if ((Test-Path $mysqlPidFile) -and $PSCmdlet.ShouldProcess($mysqlPidFile, 'Delete stale mysql.pid')) {
    Remove-Item -Force $mysqlPidFile
}

if ($StartAfterRepair -and $PSCmdlet.ShouldProcess($mysqldExe, 'Start MariaDB in standalone mode')) {
    Start-Process -WindowStyle Hidden -FilePath $mysqldExe -ArgumentList '--defaults-file=C:\xampp\mysql\bin\my.ini', '--standalone'
}

Write-Host "System-table snapshot saved to $snapshotDir"
Write-Host 'Repair completed.'
Write-Host 'If you had custom MySQL users, restore them from a logical backup after MySQL starts again.'
