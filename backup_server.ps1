# =========================================================================
# AUTO BACKUP SCRIPT UNTUK MTs RS (DATABASE + SOURCE CODE)
# =========================================================================

# 1. KONFIGURASI LOKASI
$BackupDir = "I:\Drive Saya\backup_MTsRS"
$SourceCodeDir = "c:\xampp\htdocs\mtsrs"
$MySQLBin = "c:\xampp\mysql\bin\mysqldump.exe"
$RetentionDays = 3

# 2. MEMBUAT FOLDER HARIAN
$DateStr = Get-Date -Format "yyyy-MM-dd"
$DailyBackupDir = Join-Path -Path $BackupDir -ChildPath "Backup_$DateStr"

if (-not (Test-Path -Path $DailyBackupDir)) {
    New-Item -ItemType Directory -Path $DailyBackupDir -Force | Out-Null
    Write-Host "Dibuat folder backup harian: $DailyBackupDir"
}

# 3. BACKUP DATABASE (DUMP)
$Databases = @("db_mts_rs")

foreach ($db in $Databases) {
    $OutputFile = Join-Path -Path $DailyBackupDir -ChildPath "$db.sql"
    Write-Host "Sedang membackup database $db ..."
    
    # Eksekusi mysqldump (Asumsi user root, tanpa password)
    $args = @("-u", "root", "$db")
    & $MySQLBin $args | Out-File -FilePath $OutputFile -Encoding utf8
}

# 4. BACKUP SOURCE CODE (ZIP)
$ZipFile = Join-Path -Path $DailyBackupDir -ChildPath "mtsrs_source.zip"
Write-Host "Sedang mengompres source code ke dalam file ZIP. Proses ini mungkin memakan waktu beberapa menit..."
if (Test-Path -Path $ZipFile) { Remove-Item -Path $ZipFile -Force }

# Agar tidak error karena file terkunci (locked files seperti logs/sessions),
# kita copy dulu ke folder temporary menggunakan robocopy, baru di-zip.
$TempDir = Join-Path -Path $env:TEMP -ChildPath "mtsrs_backup_temp_$DateStr"
if (Test-Path -Path $TempDir) { Remove-Item -Path $TempDir -Recurse -Force }
New-Item -ItemType Directory -Path $TempDir | Out-Null

# Robocopy mengcopy file dan mengabaikan file yang dilock (/R:0 /W:0)
# Kita bisa exclude folder tertentu jika perlu (misal: /XD storage\sessions)
& robocopy $SourceCodeDir $TempDir /MIR /R:0 /W:0 /NDL /NFL /NJH /NJS

# Menggunakan System.IO.Compression.ZipFile (Jauh lebih cepat)
Add-Type -AssemblyName System.IO.Compression.FileSystem
[System.IO.Compression.ZipFile]::CreateFromDirectory($TempDir, $ZipFile, [System.IO.Compression.CompressionLevel]::Optimal, $false)

# Hapus folder temporary
Remove-Item -Path $TempDir -Recurse -Force
Write-Host "Backup source code selesai!"

# 5. HAPUS BACKUP LAMA (Auto-Cleanup)
Write-Host "Mencari dan menghapus backup yang lebih tua dari $RetentionDays hari..."
if (Test-Path -Path $BackupDir) {
    Get-ChildItem -Path $BackupDir -Directory | Where-Object {
        $_.Name -match "^Backup_\d{4}-\d{2}-\d{2}$" -and $_.CreationTime -lt (Get-Date).AddDays(-$RetentionDays)
    } | Remove-Item -Recurse -Force
}

Write-Host "========================================="
Write-Host "PROSES BACKUP SELESAI!"
Write-Host "========================================="
