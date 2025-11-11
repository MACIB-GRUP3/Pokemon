pipeline {
    agent any
    
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
                    // Limpiar el workspace antes de comenzar
                    sh '''
                        echo "Limpiando workspace..."
                        rm -rf .git
                        rm -rf *
                    '''
                }
            }
        }
        
        stage('Checkout') {
            steps {
                script {
                    // Realizar checkout manual con manejo de errores
                    retry(3) {
                        checkout([
                            $class: 'GitSCM',
                            branches: [[name: '*/main']],
                            userRemoteConfigs: [[url: "${GIT_REPO}"]],
                            extensions: [
                                [$class: 'CleanBeforeCheckout'],
                                [$class: 'CloneOption', depth: 1, noTags: false, shallow: true]
                            ]
                        ])
                    }
                }
            }
        }
        
        stage('Prepare Environment') {
            steps {
                sh '''
                    echo "Listado del repositorio:"
                    ls -la
                    which php || apt-get update && apt-get install -y php php-cli php-xml php-mbstring curl
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
        
        stage('Deploy PHP Server') {
            steps {
                script {
                    sh '''
                        pkill -f "php -S" || true
                        nohup php -S 0.0.0.0:${APP_PORT} -t . > php-server.log 2>&1 &
                        echo $! > php-server.pid
                        sleep 5
                        curl -I http://localhost:${APP_PORT} || echo "Servidor PHP iniciado"
                    '''
                }
            }
        }
        
        stage('DAST - OWASP ZAP Scan') {
            steps {
                script {
                    sh '''
                        docker stop zap-pokemon || true
                        docker rm zap-pokemon || true
                        mkdir -p ${WORKSPACE}/zap-reports
                        docker run --name zap-pokemon \
                            --network host \
                            -v ${WORKSPACE}/zap-reports:/zap/wrk:rw \
                            -t owasp/zap2docker-stable \
                            zap-baseline.py \
                            -t http://localhost:${APP_PORT} \
                            -r zap_report.html \
                            -I
                    '''
                }
            }
        }
        
        stage('PHP Security Checks') {
            steps {
                script {
                    sh '''
                        echo "=== PHP Security Analysis ==="
                        grep -r "mysql_query\\|mysqli_query" . --include="*.php" | grep -v "prepare" || echo "No se encontraron queries sin preparar"
                        grep -r "echo \\$_GET\\|echo \\$_POST\\|print \\$_GET\\|print \\$_POST" . --include="*.php" || echo "No se encontraron outputs directos sin escape"
                        grep -r "include\\|require" . --include="*.php" | grep "\\$_GET\\|\\$_POST" || echo "No se encontraron inclusiones dinámicas peligrosas"
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
                // Asegurarnos de que estamos en un contexto de node
                try {
                    sh '''
                        if [ -f php-server.pid ]; then
                            kill $(cat php-server.pid) || true
                            rm php-server.pid
                        fi
                        pkill -f "php -S" || true
                        docker stop zap-pokemon || true
                        docker rm zap-pokemon || true
                    '''
                } catch (Exception e) {
                    echo "Error en limpieza: ${e.message}"
                }
            }
        }
        success {
            echo "✅ Pipeline completado exitosamente! Revisa SonarQube y ZAP."
        }
        failure {
            echo "❌ El pipeline ha fallado. Revisa los logs."
        }
    }
}
