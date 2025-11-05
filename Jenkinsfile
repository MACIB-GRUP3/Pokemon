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
        stage('Checkout') {
            steps {
                git branch: 'main', 
                    url: "${GIT_REPO}"
            }
        }
        
        stage('Prepare Environment') {
            steps {
                sh '''
                    # Verificar estructura del proyecto
                    echo "Contenido del repositorio:"
                    ls -la
                    
                    # Instalar PHP y Composer en Jenkins si es necesario
                    which php || apt-get update && apt-get install -y php php-cli php-xml php-mbstring
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
                                -Dsonar.php.coverage.reportPaths=coverage.xml \
                                -Dsonar.exclusions=**/vendor/**,**/tests/**
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
        
        stage('Deploy for DAST') {
            steps {
                script {
                    sh '''
                        # Detener servidor PHP anterior si existe
                        pkill -f "php -S" || true
                        
                        # Iniciar servidor PHP
                        nohup php -S 0.0.0.0:${APP_PORT} -t . > php-server.log 2>&1 &
                        echo $! > php-server.pid
                        
                        # Esperar a que el servidor esté listo
                        sleep 5
                        
                        # Verificar que está funcionando
                        curl -I http://localhost:${APP_PORT} || echo "Servidor PHP iniciado"
                    '''
                }
            }
        }
        
        stage('DAST - OWASP ZAP Scan') {
            steps {
                script {
                    sh '''
                        # Limpiar contenedores anteriores
                        docker stop zap-pokemon || true
                        docker rm zap-pokemon || true
                        
                        # Crear directorio para reportes
                        mkdir -p ${WORKSPACE}/zap-reports
                        
                        # Ejecutar ZAP baseline scan (más rápido para PHP)
                        docker run --name zap-pokemon \
                            --network host \
                            -v ${WORKSPACE}/zap-reports:/zap/wrk:rw \
                            -t owasp/zap2docker-stable \
                            zap-baseline.py \
                            -t http://localhost:${APP_PORT} \
                            -r zap_report.html \
                            -I
                    '''
                    
                    // Para un escaneo más completo (toma más tiempo)
                    sh '''
                        # Full scan (opcional - descomentar si se necesita)
                        # docker run --name zap-full-pokemon \
                        #     --network host \
                        #     -v ${WORKSPACE}/zap-reports:/zap/wrk:rw \
                        #     -t owasp/zap2docker-stable \
                        #     zap-full-scan.py \
                        #     -t http://localhost:${APP_PORT} \
                        #     -r zap_full_report.html \
                        #     -I
                    '''
                }
            }
        }
        
        stage('Security Analysis - PHP Specific') {
            steps {
                script {
                    // Análisis adicional de seguridad para PHP
                    sh '''
                        # Buscar patrones inseguros comunes en PHP
                        echo "=== Análisis de Seguridad PHP ==="
                        
                        # Buscar posibles SQL Injections
                        grep -r "mysql_query\\|mysqli_query" . --include="*.php" | grep -v "prepare" || echo "No se encontraron queries sin preparar"
                        
                        # Buscar posibles XSS
                        grep -r "echo \\$_GET\\|echo \\$_POST\\|print \\$_GET\\|print \\$_POST" . --include="*.php" || echo "No se encontraron outputs directos sin escape"
                        
                        # Buscar inclusiones peligrosas
                        grep -r "include\\|require" . --include="*.php" | grep "\\$_GET\\|\\$_POST" || echo "No se encontraron inclusiones dinámicas peligrosas"
                        
                        # Buscar funciones peligrosas
                        grep -r "eval\\|exec\\|system\\|shell_exec\\|passthru" . --include="*.php" || echo "No se encontraron funciones peligrosas"
                    '''
                }
            }
        }
        
        stage('Publish Reports') {
            steps {
                publishHTML([
                    allowMissing: false,
                    alwaysLinkToLastBuild: true,
                    keepAll: true,
                    reportDir: 'zap-reports',
                    reportFiles: 'zap_report.html',
                    reportName: 'OWASP ZAP Security Report'
                ])
                
                archiveArtifacts artifacts: 'zap-reports/**/*', allowEmptyArchive: true
            }
        }
    }
    
    post {
        always {
            script {
                // Limpiar recursos
                sh '''
                    # Detener servidor PHP
                    if [ -f php-server.pid ]; then
                        kill $(cat php-server.pid) || true
                        rm php-server.pid
                    fi
                    pkill -f "php -S" || true
                    
                    # Limpiar contenedores Docker
                    docker stop zap-pokemon || true
                    docker rm zap-pokemon || true
                '''
            }
        }
        success {
            echo """
            ✅ Pipeline completado exitosamente!
            📊 Revisa los reportes de:
            - SonarQube: http://[IP-VM]:9000
            - ZAP Report: En los artefactos de Jenkins
            """
        }
        failure {
            echo '❌ El pipeline ha fallado. Revisa los logs para más detalles.'
        }
    }
}
