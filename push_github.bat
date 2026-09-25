@echo off
echo ==============================================
echo Auto Push ke GitHub
echo ==============================================
echo.
set /p pesan="Masukkan pesan commit (atau tekan Enter untuk pesan otomatis): "
if "%pesan%"=="" set pesan=Update otomatis %date% %time%

echo.
echo [1/3] Menambahkan file ke Git (git add .)...
git add .

echo.
echo [2/3] Menyimpan perubahan (git commit)...
git commit -m "%pesan%"

echo.
echo [3/3] Mengirim ke GitHub (git push)...
git push origin main

echo.
echo ==============================================
echo Selesai bos! Semua file sudah terupdate di GitHub.
echo ==============================================
pause
