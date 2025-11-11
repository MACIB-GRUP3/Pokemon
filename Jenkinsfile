pipeline {
    agent any
    
    environment {
        GIT_REPO = 'https://github.com/marc-mora/pokemon.git'
        SONAR_PROJECT_KEY = 'pokemon-php'
        SONAR_PROJECT_NAME = 'Pokemon PHP App'
        APP_PORT = '8888'
        ZAP_PORT = '8090'
    }
    
    triggers {
        pollSCM('H/5 * * * *')
    }
    
    stages {
        stage('Clean Workspace') {
            steps {
                script {
                    echo "🧹 Limpiando workspace..."
                    deleteDir()
                }
            }
        }
        
        stage('Checkout') {
            steps {
                script {
                    echo "📥 Clonando repositorio desde ${GIT_REPO}"
                    retry(3) {
                        git branch: 'main', url: "${GIT_REPO}"
                    }
                    echo "✅ Checkout completado"
                }
            }
        }
        
        stage('Verify Checkout') {
            steps {
                sh '''
                    echo "📁 Contenido del workspace:"
                    ls -la
                    echo "🌿 Branch actual:"
                    git branch
                '''
            }
        }
        
        stage('Prepare Environment') {
            steps {
                sh '''
                    echo "=== 🔧 Preparando entorno PHP ==="
                    
                    # Verificar PHP
                    if ! which php > /dev/null 2>&1; then
                        echo "❌ PHP no está instalado"
                        exit 1
                    fi
                    
                    echo "PHP instalado:"
                    which php
                    php --version
                    
                    echo "✅ Entorno verificado"
                '''
            }
        }
        
        stage('SAST - SonarQube Analysis') {
            steps {
                script {
                    echo "🔍 Iniciando análisis estático con SonarQube..."
                    def scannerHome = tool 'SonarScanner'
                    withSonarQubeEnv('SonarQube') {
                        sh """
                            ${scannerHome}/bin/sonar-scanner \
                                -Dsonar.projectKey=${SONAR_PROJECT_KEY} \
                                -Dsonar.projectName='${SONAR_PROJECT_NAME}' \
                                -Dsonar.sources=. \
                                -Dsonar.language=php \
                                -Dsonar.sourceEncoding=UTF-8 \
                                -Dsonar.php.coverage.reportPaths=coverage.xml \
                                -Dsonar.exclusions=**/vendor/**,**/tests/**,**/.git/**
                        """
                    }
                    echo "✅ Análisis SonarQube completado"
                }
            }
        }
        
        stage('Quality Gate') {
            steps {
                script {
                    echo "🚦 Esperando Quality Gate de SonarQube..."
                    timeout(time: 5, unit: 'MINUTES') {
                        def qg = waitForQualityGate()
                        if (qg.status != 'OK') {
                            echo "⚠️ Quality Gate falló: ${qg.status}"
                            // No abortar el pipeline, solo advertir
                        } else {
                            echo "✅ Quality Gate aprobado"
                        }
                    }
                }
            }
        }
        
        stage('Deploy PHP Server') {
            steps {
                script {
                    sh '''
                        echo "=== 🚀 Desplegando servidor PHP ==="
                        
                        # Limpiar procesos PHP previos
                        echo "Deteniendo servidores PHP previos..."
                        pkill -f "php -S" || true
                        sleep 2
                        
                        # Iniciar servidor PHP en background
                        echo "Iniciando servidor PHP en puerto ${APP_PORT}..."
                        nohup php -S 0.0.0.0:${APP_PORT} -t . > php-server.log 2>&1 &
                        PHP_PID=$!
                        echo $PHP_PID > php-server.pid
                        echo "PID del servidor PHP: $PHP_PID"
                        
                        # Esperar a que el servidor inicie
                        echo "Esperando a que el servidor esté listo..."
                        sleep 5
                        
                        # Verificar que el servidor está corriendo
                        if ps -p $PHP_PID > /dev/null; then
                            echo "✅ Proceso PHP está corriendo (PID: $PHP_PID)"
                        else
                            echo "❌ El proceso PHP no está corriendo"
                            cat php-server.log
                            exit 1
                        fi
                        
                        # Verificar conectividad HTTP
                        echo "Verificando conectividad HTTP..."
                        max_attempts=10
                        attempt=0
                        
                        while [ $attempt -lt $max_attempts ]; do
                            if curl -f -s -o /dev/null http://localhost:${APP_PORT}; then
                                echo "✅ Servidor PHP respondiendo correctamente en http://localhost:${APP_PORT}"
                                curl -I http://localhost:${APP_PORT}
                                exit 0
                            fi
                            attempt=$((attempt + 1))
                            echo "Intento $attempt de $max_attempts..."
                            sleep 2
                        done
                        
                        echo "❌ Servidor PHP no responde después de $max_attempts intentos"
                        echo "Logs del servidor:"
                        cat php-server.log
                        exit 1
                    '''
                }
            }
        }
        
        stage('DAST - OWASP ZAP Scan') {
            steps {
                script {
                    sh '''
                        echo "=== 🔒 Ejecutando OWASP ZAP Scan ==="
                        
                        # Limpiar contenedores ZAP previos
                        echo "Limpiando contenedores ZAP anteriores..."
                        docker stop zap-pokemon 2>/dev/null || true
                        docker rm zap-pokemon 2>/dev/null || true
                        
                        # Crear y configurar directorio de reportes
                        echo "Configurando directorio de reportes..."
                        mkdir -p ${WORKSPACE}/zap-reports
                        chmod -R 777 ${WORKSPACE}/zap-reports
                        
                        # Obtener IP del host para que ZAP pueda conectarse
                        echo "Detectando IP del host..."
                        HOST_IP=""
                        
                        # Método 1: comando ip
                        if command -v ip &> /dev/null; then
                            HOST_IP=$(ip route | grep default | awk '{print $3}' | head -n1)
                            echo "IP detectada con 'ip route': $HOST_IP"
                        fi
                        
                        # Método 2: hostname -I (alternativa)
                        if [ -z "$HOST_IP" ] && command -v hostname &> /dev/null; then
                            HOST_IP=$(hostname -I | awk '{print $1}')
                            echo "IP detectada con 'hostname -I': $HOST_IP"
                        fi
                        
                        # Método 3: Gateway por defecto de Docker
                        if [ -z "$HOST_IP" ]; then
                            HOST_IP="172.17.0.1"
                            echo "⚠️  Usando IP por defecto del gateway Docker: $HOST_IP"
                        fi
                        
                        echo "🌐 IP final para ZAP: $HOST_IP"
                        
                        # Verificar que la app es accesible antes de ZAP
                        echo "Verificando accesibilidad de la aplicación..."
                        if ! curl -f -s http://localhost:${APP_PORT} > /dev/null; then
                            echo "❌ La aplicación no está accesible en localhost:${APP_PORT}"
                            echo "Logs del servidor PHP:"
                            cat php-server.log || true
                            exit 1
                        fi
                        echo "✅ Aplicación accesible"
                        
                        # Descargar imagen de ZAP
                        echo "📦 Descargando imagen OWASP ZAP..."
                        docker pull ghcr.io/zaproxy/zaproxy:stable
                        
                        # Ejecutar ZAP baseline scan
                        echo "🚀 Ejecutando ZAP baseline scan..."
                        echo "Target: http://localhost:${APP_PORT}"
                        
                        set +e  # No detener el script si ZAP encuentra vulnerabilidades
                        
                        docker run --name zap-pokemon \
                            --network host \
                            -v ${WORKSPACE}/zap-reports:/zap/wrk:rw \
                            -u zap \
                            ghcr.io/zaproxy/zaproxy:stable \
                            zap-baseline.py \
                            -t http://localhost:${APP_PORT} \
                            -r zap_report.html \
                            -J zap_report.json \
                            -w zap_report.md \
                            -I
                        
                        ZAP_EXIT_CODE=$?
                        set -e
                        
                        echo "ZAP scan finalizado con código: $ZAP_EXIT_CODE"
                        
                        # ZAP retorna diferentes códigos según vulnerabilidades encontradas
                        # 0 = sin problemas, 1 = warnings, 2 = fallos
                        if [ $ZAP_EXIT_CODE -eq 0 ]; then
                            echo "✅ ZAP scan completado sin problemas"
                        elif [ $ZAP_EXIT_CODE -eq 1 ]; then
                            echo "⚠️  ZAP scan completado con advertencias"
                        elif [ $ZAP_EXIT_CODE -eq 2 ]; then
                            echo "🔴 ZAP scan encontró vulnerabilidades"
                        else
                            echo "⚠️  ZAP scan completado con código: $ZAP_EXIT_CODE"
                        fi
                        
                        # Verificar si se generaron los reportes
                        echo "📊 Verificando reportes generados..."
                        if [ -f "${WORKSPACE}/zap-reports/zap_report.html" ]; then
                            echo "✅ Reporte HTML generado correctamente"
                            ls -lh ${WORKSPACE}/zap-reports/zap_report.html
                        else
                            echo "❌ No se generó el reporte HTML"
                        fi
                        
                        echo "Contenido del directorio de reportes:"
                        ls -la ${WORKSPACE}/zap-reports/ || true
                        
                        # No fallar el pipeline incluso si ZAP encuentra problemas
                        exit 0
                    '''
                }
            }
        }
        
        stage('PHP Security Checks') {
            steps {
                script {
                    sh '''
                        echo "=== 🔐 Análisis de Seguridad PHP ==="
                        
                        echo "🔍 1. Buscando SQL Injection potenciales..."
                        echo "   (queries sin prepared statements)"
                        if grep -rn "mysql_query\\|mysqli_query" . --include="*.php" | grep -v "prepare"; then
                            echo "   ⚠️  Se encontraron queries sin prepared statements"
                        else
                            echo "   ✅ No se encontraron queries directas sin preparar"
                        fi
                        
                        echo ""
                        echo "🔍 2. Buscando XSS potenciales..."
                        echo "   (outputs sin escape)"
                        if grep -rn "echo \\$_GET\\|echo \\$_POST\\|print \\$_GET\\|print \\$_POST" . --include="*.php"; then
                            echo "   ⚠️  Se encontraron outputs directos sin escape"
                        else
                            echo "   ✅ No se encontraron outputs directos sin escape"
                        fi
                        
                        echo ""
                        echo "🔍 3. Buscando File Inclusion vulnerabilidades..."
                        echo "   (include/require con variables de usuario)"
                        if grep -rn "include\\|require" . --include="*.php" | grep "\\$_GET\\|\\$_POST"; then
                            echo "   🔴 CRÍTICO: Se encontraron inclusiones dinámicas peligrosas"
                        else
                            echo "   ✅ No se encontraron inclusiones dinámicas peligrosas"
                        fi
                        
                        echo ""
                        echo "🔍 4. Buscando funciones peligrosas..."
                        echo "   (eval, exec, system, shell_exec, passthru)"
                        if grep -rn "\\beval\\b\\|\\bexec\\b\\|\\bsystem\\b\\|\\bshell_exec\\b\\|\\bpassthru\\b" . --include="*.php"; then
                            echo "   ⚠️  Se encontraron funciones potencialmente peligrosas"
                        else
                            echo "   ✅ No se encontraron funciones peligrosas"
                        fi
                        
                        echo ""
                        echo "🔍 5. Buscando credenciales hardcodeadas..."
                        if grep -rn "password\\s*=\\s*['\"]\\|pwd\\s*=\\s*['\"]" . --include="*.php" | grep -v "\\$_"; then
                            echo "   ⚠️  Posibles credenciales hardcodeadas encontradas"
                        else
                            echo "   ✅ No se encontraron credenciales hardcodeadas obvias"
                        fi
                        
                        echo ""
                        echo "✅ Análisis de seguridad PHP completado"
                    '''
                }
            }
        }
        
        stage('Publish Reports') {
            steps {
                script {
                    echo "📊 Publicando reportes..."
                    
                    // Publicar reporte HTML de ZAP
                    if (fileExists('zap-reports/zap_report.html')) {
                        publishHTML([
                            allowMissing: false,
                            alwaysLinkToLastBuild: true,
                            keepAll: true,
                            reportDir: 'zap-reports',
                            reportFiles: 'zap_report.html',
                            reportName: 'OWASP ZAP Security Report',
                            reportTitles: 'ZAP Security Scan'
                        ])
                        echo "✅ Reporte ZAP HTML publicado"
                    } else {
                        echo "⚠️  No se encontró reporte ZAP HTML"
                    }
                    
                    // Archivar todos los artefactos
                    archiveArtifacts artifacts: 'zap-reports/**/*', allowEmptyArchive: true, fingerprint: true
                    archiveArtifacts artifacts: 'php-server.log', allowEmptyArchive: true, fingerprint: true
                    
                    echo "✅ Reportes publicados y archivados"
                }
            }
        }
    }
    
    post {
        always {
            script {
                echo "=== 🧹 Limpieza de recursos ==="
                sh '''
                    # Detener servidor PHP
                    echo "Deteniendo servidor PHP..."
                    if [ -f php-server.pid ]; then
                        PID=$(cat php-server.pid)
                        if ps -p $PID > /dev/null 2>&1; then
                            kill $PID || true
                            echo "Servidor PHP (PID: $PID) detenido"
                        fi
                        rm -f php-server.pid
                    fi
                    
                    # Matar cualquier proceso PHP restante
                    pkill -f "php -S" || true
                    
                    # Limpiar contenedores Docker de ZAP
                    echo "Limpiando contenedores ZAP..."
                    docker stop zap-pokemon 2>/dev/null || true
                    docker rm zap-pokemon 2>/dev/null || true
                    
                    echo "✅ Limpieza completada"
                '''
            }
        }
        success {
            script {
                def sonarUrl = env.SONAR_HOST_URL ?: 'http://localhost:9000'
                echo """
╔════════════════════════════════════════════════════════════╗
║          ✅ PIPELINE COMPLETADO EXITOSAMENTE              ║
╚════════════════════════════════════════════════════════════╝

📊 REPORTES DISPONIBLES:
   
   🔍 SonarQube (SAST):
      ${sonarUrl}/dashboard?id=${SONAR_PROJECT_KEY}
   
   🔒 OWASP ZAP (DAST):
      Disponible en los artefactos de Jenkins
      o en la sección "OWASP ZAP Security Report"

⚠️  PRÓXIMOS PASOS:
   1. Revisa el Quality Gate en SonarQube
   2. Analiza las vulnerabilidades encontradas por ZAP
   3. Corrige las issues de seguridad PHP detectadas
   4. Considera implementar:
      - Prepared statements para todas las queries SQL
      - Input validation y sanitization
      - Output escaping (htmlspecialchars)
      - CSRF tokens
      - Content Security Policy headers

"""
            }
        }
        failure {
            echo """
╔════════════════════════════════════════════════════════════╗
║               ❌ EL PIPELINE HA FALLADO                   ║
╚════════════════════════════════════════════════════════════╝

🔍 PASOS PARA DEPURAR:
   1. Revisa los logs de cada stage arriba
   2. Verifica el archivo php-server.log (artefactos)
   3. Comprueba la conectividad de red
   4. Verifica que Docker está funcionando
   5. Revisa los logs de Jenkins

💡 ERRORES COMUNES:
   - Servidor PHP no inicia → Verifica puerto ${APP_PORT}
   - ZAP no conecta → Revisa firewall/red
   - SonarQube falla → Verifica configuración en Jenkins
"""
        }
        unstable {
            echo "⚠️  Pipeline completado pero inestable. Revisa las advertencias."
        }
    }
}
