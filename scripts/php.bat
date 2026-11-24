@echo off
setlocal
set "PHP_HOME=%~dp0..\tools\php"
if not exist "%PHP_HOME%\php.exe" (
    echo PHP is missing. See docs/windows-setup.md.
    exit /b 1
)
rem The assignment uses numeric OIDs; do not load unavailable named MIBs.
set "MIBS=:"
pushd "%~dp0.."
"tools\php\php.exe" -c "tools\php\php.ini" %*
set "PHP_EXIT=%errorlevel%"
popd
exit /b %PHP_EXIT%
