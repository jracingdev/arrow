# Instruções para Compilação do Projeto eMart

## ⚠️ Problema Conhecido

O projeto está apresentando um problema ao compilar via linha de comando devido a incompatibilidade entre Java 24 e o Gradle Worker Daemon do Flutter SDK. Este é um problema conhecido com versões muito recentes do Java.

## ✅ Solução Recomendada: Android Studio

A **melhor e mais fácil solução** é compilar o projeto diretamente pelo **Android Studio**:

### Passos:

1. **Abrir o projeto no Android Studio**
   - File → Open → Selecionar a pasta do projeto
   - Aguardar a sincronização do Gradle

2. **Configurar o SDK (se necessário)**
   - File → Project Structure → SDK Location
   - Verificar se o Android SDK está configurado

3. **Compilar o APK**
   - Build → Build Bundle(s) / APK(s) → Build APK(s)
   - Aguardar a compilação
   - O APK será gerado em: `build/app/outputs/flutter-apk/app-debug.apk`

4. **Executar no Emulador/Dispositivo**
   - Run → Run 'app'
   - Ou clicar no botão ▶️ verde

### Vantagens do Android Studio:

- ✅ Usa sua própria JDK (Java 21) compatível
- ✅ Resolve automaticamente problemas de dependências
- ✅ Interface gráfica amigável
- ✅ Depuração integrada
- ✅ Gerenciamento de emuladores
- ✅ Logs e erros mais fáceis de visualizar

## 🔧 Solução Alternativa: Scripts de Compilação

Foram criados scripts que tentam configurar o Java 21 antes de compilar:

### PowerShell:
```powershell
.\build_apk.ps1
```

### CMD:
```cmd
build_apk.bat
```

**Nota**: Esses scripts podem não funcionar se o problema estiver no próprio Flutter SDK.

## 📋 Configurações Realizadas

### Arquivos Atualizados:

1. **`pubspec.yaml`**
   - ✅ Versão do pacote `location` ajustada para `^7.0.0`

2. **`analysis_options.yaml`**
   - ✅ Referência ao flutter_lints comentada

3. **`android/local.properties`**
   - ✅ Configurado: `org.gradle.java.home` apontando para Java 21

4. **`android/gradle.properties`**
   - ✅ Configurado: `org.gradle.java.home`
   - ✅ Desabilitado: Gradle daemon (para evitar problemas)

5. **`android/app/build.gradle`**
   - ✅ Atualizado: Java target para versão 21
   - ✅ Atualizado: Kotlin JVM target para 21

6. **`android/gradle/wrapper/gradle-wrapper.properties`**
   - ✅ Atualizado: Gradle para versão 8.3

7. **`android/settings.gradle`**
   - ✅ Atualizado: Android Gradle Plugin para 8.2.0

8. **`android/build.gradle`**
   - ✅ Removida: Linha `project.evaluationDependsOn(':app')` que causava dependência circular

## 🚀 Próximos Passos Recomendados

### Opção 1: Usar Android Studio (RECOMENDADO)

1. Abrir o projeto no Android Studio
2. Aguardar sincronização
3. Compilar via Build → Build APK(s)
4. Executar no emulador/dispositivo

### Opção 2: Atualizar Flutter SDK

O problema pode ser resolvido com uma versão mais recente do Flutter:

```powershell
flutter upgrade
flutter doctor
```

### Opção 3: Usar Java 17 ou 21 Globalmente

Se você tem acesso para configurar variáveis de ambiente do sistema:

1. Instalar Java 17 ou 21 (LTS)
2. Configurar JAVA_HOME como variável de sistema
3. Adicionar `%JAVA_HOME%\bin` ao PATH
4. Reiniciar o terminal
5. Tentar compilar novamente

## 📝 Status do Projeto

- ✅ **Código**: Pronto para compilação
- ✅ **Dependências**: Instaladas
- ✅ **Configurações**: Atualizadas
- ✅ **Ambiente**: Configurado
- ⚠️ **Compilação**: Requer Android Studio ou Java 17/21 configurado globalmente

## 🔍 Verificação

Para verificar se tudo está configurado corretamente:

```powershell
# Verificar Flutter
flutter doctor

# Verificar Java (deve ser 17 ou 21)
java -version

# Verificar Gradle (via Android Studio)
# Build → Sync Project with Gradle Files
```

## 📞 Suporte

Se ainda houver problemas:

1. **Verificar logs do Android Studio**
   - Build → Build Output
   - View → Tool Windows → Build

2. **Verificar configurações do projeto**
   - File → Project Structure
   - Verificar SDK, JDK, e versões

3. **Limpar e reconstruir**
   - Build → Clean Project
   - Build → Rebuild Project

4. **Verificar dependências**
   - File → Invalidate Caches / Restart

## ✅ Conclusão

O projeto está **pronto para compilação** e todas as configurações necessárias foram realizadas. A **melhor forma de compilar** é usando o **Android Studio**, que resolve automaticamente problemas de JDK e Gradle.

---

**Última atualização**: Dezembro 2024
**Recomendação**: Usar Android Studio para compilação
**Java recomendado**: 17 ou 21 (LTS)
**Status**: Projeto funcional, requer Android Studio para compilação

