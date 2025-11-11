pipeline {
    agent any
    
    environment {
        GIT_REPO = 'https://github.com/MACIB-GRUP3/Pokemon.git'
        SONAR_PROJECT_KEY = 'pokemon-php'
        SONAR_PROJECT_NAME = 'Pokemon PHP App'
        APP_PORT = '8888'
        ZAP_PORT = '8090'
        // Obtener la IP del host de Docker para comunicación entre contenedores
        DOCKER_HOST_IP = sh(script: "ip route | grep docker0 | awk '{print \$9}'", returnStdout: true).trim()
    }
    
    triggers {
        pollSCM('H/5 * * * *')
    }
    
    stages {
        stage('Checkout') {
            steps {
                git branch: 'main', 
                    url: "${GIT_REPO}"
            }
        }
        
        stage('Prepare Environment') {
            steps {
                sh '''
                    echo "=== Verificando estructura del proyecto ==="
                    ls -la
                    
                    echo "=== Verificando PHP ==="
                    php --version || echo "PHP no encontrado"
                    
                    echo "=== Verificando Docker ==="
                    docker --version
                '''
            }
        }
        
        stage('SAST - SonarQube Analysis') {
            steps {
                script {
                    def scannerHome = tool 'SonarScanner'
                    withSonarQubeEnv('SonarQube') {
                        sh """
                            ${scannerHome}/bin/sonar-scanner \
                                -Dsonar.projectKey=${SONAR_PROJECT_KEY} \
                                -Dsonar.projectName='${SONAR_PROJECT_NAME}' \
                                -Dsonar.sources=. \
                                -Dsonar.language=php \
                                -Dsonar.sourceEncoding=UTF-8 \
                                -Dsonar.exclusions=**/vendor/**,**/tests/**,**/node_modules/**
                        """
                    }
                }
            }
        }
        
        stage('Quality Gate') {
            steps {
                timeout(time: 5, unit: 'MINUTES') {
                    waitForQualityGate abortPipeline: false
                }
            }
        }
        
        stage('Deploy PHP App for DAST') {
            steps {
                script {
                    sh '''
                        echo "=== Deteniendo servidores PHP anteriores ==="
                        docker stop pokemon-php-app || true
                        docker rm pokemon-php-app || true
                        
                        echo "=== Creando red Docker si no existe ==="
                        docker network create pokemon-network || true
                        
                        echo "=== Iniciando aplicación PHP en Docker ==="
                        docker run -d \
                            --name pokemon-php-app \
                            --network pokemon-network \
                            -p ${APP_PORT}:80 \
                            -v ${WORKSPACE}:/var/www/html \
                            -w /var/www/html \
                            php:8.1-apache
                        
                        echo "=== Configurando Apache en el contenedor ==="
                        docker exec pokemon-php-app bash -c "a2enmod rewrite && service apache2 restart"
                        
                        echo "=== Esperando que el servidor esté listo ==="
                        sleep 10
                        
                        echo "=== Verificando que la aplicación responde ==="
                        for i in {1..10}; do
                            if curl -f http://localhost:${APP_PORT}; then
                                echo "✅ Aplicación respondiendo correctamente"
                                break
                            else
                                echo "⏳ Intento $i/10 - Esperando respuesta..."
                                sleep 3
                            fi
                        done
                    '''
                }
            }
        }
        
        stage('DAST - OWASP ZAP Scan') {
            steps {
                script {
                    sh '''
                        echo "=== Limpiando contenedores ZAP anteriores ==="
                        docker stop zap-pokemon || true
                        docker rm zap-pokemon || true
                        
                        echo "=== Creando directorio para reportes ==="
                        mkdir -p ${WORKSPACE}/zap-reports
                        chmod -R 777 ${WORKSPACE}/zap-reports
                        
                        echo "=== Ejecutando OWASP ZAP Baseline Scan ==="
                        docker run --name zap-pokemon \
                            --network pokemon-network \
                            -v ${WORKSPACE}/zap-reports:/zap/wrk:rw \
                            -t ghcr.io/zaproxy/zaproxy:stable \
                            zap-baseline.py \
                            -t http://pokemon-php-app:80 \
                            -r zap_report.html \
                            -w zap_report.md \
                            -J zap_report.json \
                            -I || true
                        
                        echo "=== Verificando reportes generados ==="
                        ls -lh ${WORKSPACE}/zap-reports/
                    '''
                }
            }
        }
        
        stage('Security Analysis - PHP Specific') {
            steps {
                script {
                    sh '''
                        echo "=== Análisis de Seguridad Específico para PHP ==="
                        
                        echo "📌 Buscando posibles SQL Injections..."
                        grep -rn "mysql_query\\|mysqli_query" . --include="*.php" | grep -v "prepare" || echo "✅ No se encontraron queries sin preparar"
                        
                        echo "📌 Buscando posibles vulnerabilidades XSS..."
                        grep -rn "echo \\$_GET\\|echo \\$_POST\\|print \\$_GET\\|print \\$_POST" . --include="*.php" || echo "✅ No se encontraron outputs directos sin escape"
                        
                        echo "📌 Buscando inclusiones dinámicas peligrosas..."
                        grep -rn "include.*\\$\\|require.*\\$" . --include="*.php" | grep -E "\\$_GET|\\$_POST|\\$_REQUEST" || echo "✅ No se encontraron inclusiones dinámicas peligrosas"
                        
                        echo "📌 Buscando funciones peligrosas..."
                        grep -rn "\\beval\\(\\|\\bexec\\(\\|\\bsystem\\(\\|\\bshell_exec\\(\\|\\bpassthru\\(" . --include="*.php" || echo "✅ No se encontraron funciones peligrosas"
                        
                        echo "📌 Buscando archivos con permisos de escritura inseguros..."
                        grep -rn "chmod.*777\\|chmod.*666" . --include="*.php" || echo "✅ No se encontraron permisos inseguros"
                        
                        echo "=== Análisis completado ==="
                    '''
                }
            }
        }
        
        stage('Publish Reports') {
            steps {
                script {
                    // Publicar reporte HTML de ZAP
                    publishHTML([
                        allowMissing: false,
                        alwaysLinkToLastBuild: true,
                        keepAll: true,
                        reportDir: 'zap-reports',
                        reportFiles: 'zap_report.html',
                        reportName: 'OWASP ZAP Security Report',
                        reportTitles: 'ZAP Security Scan'
                    ])
                    
                    // Archivar todos los reportes
                    archiveArtifacts artifacts: 'zap-reports/**/*', allowEmptyArchive: true, fingerprint: true
                    
                    echo "📊 Reportes publicados exitosamente"
                }
            }
        }
    }
    
    post {
        always {
            script {
                echo "=== Limpiando recursos ==="
                sh '''
                    # Detener y eliminar contenedor PHP
                    docker stop pokemon-php-app || true
                    docker rm pokemon-php-app || true
                    
                    # Detener y eliminar contenedor ZAP
                    docker stop zap-pokemon || true
                    docker rm zap-pokemon || true
                    
                    # Limpiar red (opcional - comentar si causa problemas)
                    # docker network rm pokemon-network || true
                    
                    echo "✅ Limpieza completada"
                '''
            }
        }
        success {
            echo """
            ╔═══════════════════════════════════════════════════╗
            ║  ✅ PIPELINE COMPLETADO EXITOSAMENTE             ║
            ╚═══════════════════════════════════════════════════╝
            
            📊 Revisa los reportes de seguridad:
            ├─ SonarQube: http://localhost:9000
            └─ ZAP Report: Disponible en los artefactos de Jenkins
            
            🔍 Proyecto SonarQube: ${SONAR_PROJECT_KEY}
            """
        }
        failure {
            echo """
            ╔═══════════════════════════════════════════════════╗
            ║  ❌ EL PIPELINE HA FALLADO                       ║
            ╚═══════════════════════════════════════════════════╝
            
            🔍 Revisa los logs de cada stage para identificar el problema
            💡 Verifica que:
               - SonarQube esté funcionando (puerto 9000)
               - Docker esté disponible en Jenkins
               - Los puertos ${APP_PORT} y ${ZAP_PORT} estén libres
            """
        }
    }
}
