@echo off
REM Script para compilar o APK usando Java 21 do Android Studio
REM Este script configura o JAVA_HOME antes de compilar

set JAVA_HOME=C:\Program Files\Android\Android Studio\jbr
set PATH=%JAVA_HOME%\bin;%PATH%

echo Configurando Java 21 do Android Studio...
java -version

echo Limpando projeto...
call flutter clean

echo Obtendo dependencias...
call flutter pub get

echo Compilando APK...
call flutter build apk --debug

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ========================================
    echo APK compilado com sucesso!
    echo Localizacao: build\app\outputs\flutter-apk\app-debug.apk
    echo ========================================
) else (
    echo.
    echo ========================================
    echo Erro ao compilar APK
    echo ========================================
)

pause

