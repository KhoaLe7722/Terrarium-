# XAMPP MySQL recovery notes

## Vi sao MySQL hay loi trong may nay

Mau log tren may nay cho thay 2 van de lap lai:

- Windows co nhieu lan restart/shutdown khong sach, va MySQL phai chay `crash recovery` o nhieu lan khoi dong sau do.
- `mysqld.exe` da bi `APPCRASH` tren Windows, sau do cac file Aria system table nhu `aria_log_control`, `global_priv`, `db`, `plugin` bi hong.

Khi MySQL duoc chay tu XAMPP Control Panel nhu mot app thong thuong, no de bi cat ngang hon luc restart may, logout, treo may, mat dien, hoac khi XAMPP bi dong gap.

## Dau hieu nhan biet

- `mysql_error.log` co `Starting crash recovery`.
- Sau vai lan, log chuyen thanh `Aria recovery failed`.
- XAMPP hien `MySQL shutdown unexpectedly`.

## Cach phong ngua

1. Dung MySQL trong XAMPP truoc khi restart hoac tat may.
2. Khong force close XAMPP Control Panel khi MySQL dang chay.
3. Backup dinh ky bang `scripts/mysql-backup.ps1`.
4. Neu co the, uu tien chay stack Docker cua project de giam rui ro file data cua XAMPP bi hong.

## Quy trinh backup nhanh

Lenh nay tao:

- `terrarium_db.sql`
- `mysql-users.sql`
- `backup-info.txt`

Command:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\mysql-backup.ps1
```

Backup se nam trong `backups/mysql/<timestamp>/`.

## Quy trinh restore nhanh

Khi MySQL da chay lai binh thuong:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\mysql-restore.ps1 -BackupDir .\backups\mysql\<timestamp>
```

Script restore se import lai:

- database `terrarium_db`
- user va grants trong `mysql-users.sql` neu file nay co ton tai

## Quy trinh repair khi XAMPP hong system tables

Neu log bao `Aria recovery failed`, `Incorrect file format 'global_priv'`, hoac MySQL vua start da stop:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\xampp-mysql-repair.ps1 -StartAfterRepair
```

Script nay se:

1. Sao luu `C:\xampp\mysql\data\mysql`, `terrarium_db`, `aria_log*`, `mysql_error.log`, `mysqld.dmp`.
2. Thay `C:\xampp\mysql\data\mysql` bang bo system tables mac dinh trong `C:\xampp\mysql\backup\mysql`.
3. Xoa `aria_log` cu de MariaDB tao lai.
4. Tuy chon khoi dong lai MariaDB.

Luu y:

- Script repair co the reset MySQL users ve mac dinh cua XAMPP.
- Sau repair, neu ban tung tao user rieng, hay chay lai `scripts/mysql-restore.ps1` de import grants tu backup.
