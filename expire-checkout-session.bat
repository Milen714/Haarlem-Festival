@echo off
setlocal EnableExtensions

set "envFile=%~dp0.env"

if not exist "%envFile%" (
    echo Could not find .env file at: %envFile%
    exit /b 1
)

set "secretKey="
for /f "usebackq tokens=1,* delims==" %%A in ("%envFile%") do (
    if /I "%%A"=="STRIPE_SECRET_KEY" set "secretKey=%%B"
)

if not defined secretKey (
    echo STRIPE_SECRET_KEY was not found in .env
    exit /b 1
)

echo.
set /p "sessionId=Enter Stripe checkout session ID (cs_test_...): "

if "%sessionId%"=="" (
    echo Session ID is required.
    exit /b 1
)

powershell -NoProfile -ExecutionPolicy Bypass -Command ^
  "$ErrorActionPreference='Stop';" ^
  "$sessionId='%sessionId%';" ^
  "$secretKey='%secretKey%';" ^
  "Invoke-RestMethod -Method Post -Uri ('https://api.stripe.com/v1/checkout/sessions/{0}/expire' -f $sessionId) -Headers @{ Authorization = ('Bearer {0}' -f $secretKey) } | Format-List"

if errorlevel 1 (
    echo Request failed.
    exit /b 1
)

echo Request completed.
endlocal
