pipeline {
    agent any
    
    options {
        // Deshabilitar el checkout automático
        skipDefaultCheckout(true)
    }
    
    environment {
        GIT_REPO = 'https://github.com/MACIB-GRUP3/Pokemon.git'
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
                    echo "Limpiando workspace..."
                    deleteDir()
                }
            }
        }
        
        stage('Checkout') {
            steps {
                script {
                    echo "Clonando repositorio desde ${GIT_REPO}"
                    retry(3) {
                        checkout([
                            $class: 'GitSCM',
                            branches: [[name: '*/main']],
                            userRemoteConfigs: [[url: "${GIT_REPO}"]],
                            extensions: [
                                [$class: 'CleanBeforeCheckout'],
                                [$class: 'CloneOption', depth: 1, noTags: false, shallow: true, timeout: 10]
                            ]
                        ])
                    }
                    echo "Checkout completado exitosamente"
                }
            }
        }
        
        stage('Verify Checkout') {
            steps {
                sh '''
                    echo "Contenido del workspace:"
                    ls -la
                    echo "Branch actual:"
                    git branch
                '''
            }
        }
        
        stage('Prepare Environment') {
            steps {
                sh '''
                    echo "=== Preparando entorno PHP ==="
                    which php || (apt-get update && apt-get install -y php php-cli php-xml php-mbstring curl)
                    php --version
                '''
            }
        }
        
        stage('SAST - SonarQube Analysis') {
            steps {
                script {
                    try {
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
                                    -Dsonar.exclusions=**/vendor/**,**/tests/**
                            """
                        }
                    } catch (Exception e) {
                        echo "⚠️ SonarQube analysis falló: ${e.message}"
                        currentBuild.result = 'UNSTABLE'
                    }
                }
            }
        }
        
        stage('Quality Gate') {
            steps {
                script {
                    try {
                        timeout(time: 5, unit: 'MINUTES') {
                            waitForQualityGate abortPipeline: false
                        }
                    } catch (Exception e) {
                        echo "⚠️ Quality Gate no disponible: ${e.message}"
                    }
                }
            }
        }
        
        stage('Deploy PHP Server') {
            steps {
                script {
                    sh '''
                        echo "=== Iniciando servidor PHP ==="
                        # Matar cualquier servidor PHP previo
                        pkill -f "php -S" || true
                        
                        # Iniciar servidor PHP
                        nohup php -S 0.0.0.0:${APP_PORT} -t . > php-server.log 2>&1 &
                        echo $! > php-server.pid
                        
                        # Esperar a que el servidor inicie
                        sleep 5
                        
                        # Verificar que el servidor está corriendo
                        if curl -I http://localhost:${APP_PORT}; then
                            echo "✅ Servidor PHP corriendo en puerto ${APP_PORT}"
                        else
                            echo "❌ Error: Servidor PHP no responde"
                            cat php-server.log
                            exit 1
                        fi
                    '''
                }
            }
        }
        
        stage('DAST - OWASP ZAP Scan') {
            steps {
                script {
                    sh '''
                        echo "=== Ejecutando OWASP ZAP Scan ==="
                        
                        # Limpiar contenedores previos
                        docker stop zap-pokemon 2>/dev/null || true
                        docker rm zap-pokemon 2>/dev/null || true
                        
                        # Crear directorio para reportes
                        mkdir -p ${WORKSPACE}/zap-reports
                        chmod 777 ${WORKSPACE}/zap-reports
                        
                        # Ejecutar ZAP scan
                        docker run --name zap-pokemon \
                            --network host \
                            -v ${WORKSPACE}/zap-reports:/zap/wrk:rw \
                            -t owasp/zap2docker-stable \
                            zap-baseline.py \
                            -t http://localhost:${APP_PORT} \
                            -r zap_report.html \
                            -I || echo "⚠️ ZAP completado con advertencias"
                        
                        echo "✅ ZAP scan completado"
                    '''
                }
            }
        }
        
        stage('PHP Security Checks') {
            steps {
                script {
                    sh '''
                        echo "=== Análisis de Seguridad PHP ==="
                        
                        echo "🔍 Buscando queries SQL sin preparar..."
                        grep -r "mysql_query\\|mysqli_query" . --include="*.php" | grep -v "prepare" || echo "✅ No se encontraron queries sin preparar"
                        
                        echo "🔍 Buscando outputs sin escape..."
                        grep -r "echo \\$_GET\\|echo \\$_POST\\|print \\$_GET\\|print \\$_POST" . --include="*.php" || echo "✅ No se encontraron outputs directos sin escape"
                        
                        echo "🔍 Buscando inclusiones dinámicas peligrosas..."
                        grep -r "include\\|require" . --include="*.php" | grep "\\$_GET\\|\\$_POST" || echo "✅ No se encontraron inclusiones dinámicas peligrosas"
                        
                        echo "🔍 Buscando funciones peligrosas..."
                        grep -r "eval\\|exec\\|system\\|shell_exec\\|passthru" . --include="*.php" || echo "✅ No se encontraron funciones peligrosas"
                        
                        echo "✅ Análisis de seguridad completado"
                    '''
                }
            }
        }
        
        stage('Publish Reports') {
            steps {
                script {
                    try {
                        publishHTML([
                            allowMissing: false,
                            alwaysLinkToLastBuild: true,
                            keepAll: true,
                            reportDir: 'zap-reports',
                            reportFiles: 'zap_report.html',
                            reportName: 'OWASP ZAP Security Report'
                        ])
                        
                        archiveArtifacts artifacts: 'zap-reports/**/*', allowEmptyArchive: true
                        
                        echo "✅ Reportes publicados"
                    } catch (Exception e) {
                        echo "⚠️ Error publicando reportes: ${e.message}"
                    }
                }
            }
        }
    }
    
    post {
        always {
            script {
                try {
                    sh '''
                        echo "=== Limpieza final ==="
                        
                        # Detener servidor PHP
                        if [ -f php-server.pid ]; then
                            kill $(cat php-server.pid) 2>/dev/null || true
                            rm php-server.pid
                        fi
                        pkill -f "php -S" || true
                        
                        # Limpiar contenedores Docker
                        docker stop zap-pokemon 2>/dev/null || true
                        docker rm zap-pokemon 2>/dev/null || true
                        
                        echo "✅ Limpieza completada"
                    '''
                } catch (Exception e) {
                    echo "⚠️ Error en limpieza: ${e.message}"
                }
            }
        }
        success {
            echo "✅ Pipeline completado exitosamente! Revisa SonarQube y ZAP."
        }
        failure {
            echo "❌ El pipeline ha fallado. Revisa los logs."
        }
        unstable {
            echo "⚠️ Pipeline completado con advertencias."
        }
    }
}
