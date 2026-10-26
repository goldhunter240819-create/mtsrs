@echo off
echo ==============================================
echo Menarik Update Terbaru dari GitHub (Git Pull)
echo MTs Roudlotus Sholihin
echo ==============================================
echo.
git config --global --add safe.directory C:/xampp/htdocs/mtsrs
git config --global --add safe.directory *
git pull origin main
echo.
echo ==============================================
echo Selesai! Server sudah terupdate ke versi terbaru.
echo ==============================================
pause
