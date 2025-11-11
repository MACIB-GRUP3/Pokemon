pipeline {
    agent any

    environment {
        GIT_REPO = 'https://github.com/MACIB-GRUP3/Pokemon.git'
        SONAR_PROJECT_KEY = 'pokemon-php'
        SONAR_PROJECT_NAME = 'Pokemon PHP App'
        SONARQUBE = 'SonarQube' // Nom del servidor configurat a Jenkins (Manage Jenkins > SonarQube servers)
        SCANNER_HOME = tool 'SonarScanner'
    }

    stages {

        stage('Checkout') {
            steps {
                script {
                    sh '''
                        echo "⚙️ Configurant Git per utilitzar GnuTLS..."
                        git config --global http.sslBackend gnutls
                    '''
                    git branch: 'main', url: "${GIT_REPO}"
                }
            }
        }

        stage('Prepare Environment') {
            steps {
                sh '''
                    echo "=== Verificant estructura del projecte ==="
                    ls -la
                    echo "=== Instal·lant PHP i Composer si cal ==="
                    which php || apt update && apt install -y php
                '''
            }
        }

        stage('SAST - SonarQube Analysis') {
            steps {
                script {
                    withSonarQubeEnv("${SONARQUBE}") {
                        sh """
                            echo "=== Executant anàlisi SonarQube ==="
                            ${SCANNER_HOME}/bin/sonar-scanner \
                                -Dsonar.projectKey=${SONAR_PROJECT_KEY} \
                                -Dsonar.projectName="${SONAR_PROJECT_NAME}" \
                                -Dsonar.sources=. \
                                -Dsonar.language=php \
                                -Dsonar.sourceEncoding=UTF-8 \
                                -Dsonar.php.coverage.reportPaths=coverage.xml \
                                -Dsonar.exclusions="**/vendor/**,**/tests/**"
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
                        echo "=== Desplegant aplicació PHP per a DAST ==="
                        docker network create zapnet || true
                        docker stop php-pokemon || true
                        docker rm php-pokemon || true

                        docker run -d --name php-pokemon --network zapnet \
                            -v $(pwd):/var/www/html \
                            -p 8080:80 php:8.2-apache

                        echo "Esperant 10 segons per a que Apache estigui llest..."
                        sleep 10
                    '''
                }
            }
        }

        stage('DAST - OWASP ZAP Scan') {
            steps {
                script {
                    sh '''
                        echo "=== Executant escaneig OWASP ZAP ==="
                        docker stop zap-pokemon || true
                        docker rm zap-pokemon || true

                        docker run --rm --name zap-pokemon --network zapnet \
                            -v $(pwd)/zap-reports:/zap/wrk/:rw \
                            owasp/zap2docker-stable zap-full-scan.py \
                            -t http://php-pokemon:80 \
                            -r zap_report.html || true

                        echo "=== Informes de ZAP guardats a ./zap-reports/zap_report.html ==="
                    '''
                }
            }
        }

        stage('Security Analysis - PHP Specific') {
            steps {
                sh '''
                    echo "=== (Opcional) Anàlisi de seguretat específica de PHP ==="
                    # Aquí pots afegir eines com phpstan o php-security-checker si vols
                '''
            }
        }

        stage('Publish Reports') {
            steps {
                script {
                    echo "=== Publicant informes ==="
                    publishHTML([allowMissing: true,
                                 alwaysLinkToLastBuild: true,
                                 keepAll: true,
                                 reportDir: 'zap-reports',
                                 reportFiles: 'zap_report.html',
                                 reportName: 'OWASP ZAP DAST Report'])
                }
            }
        }
    }

    post {
        always {
            script {
                sh '''
                    echo "=== Netejant recursos ==="
                    docker stop php-pokemon || true
                    docker rm php-pokemon || true
                    docker stop zap-pokemon || true
                    docker rm zap-pokemon || true
                    docker network rm zapnet || true
                '''
            }
        }
        failure {
            echo "❌ El pipeline ha fallat. Revisa els logs per més detalls."
        }
        success {
            echo "✅ Pipeline completat correctament!"
        }
    }
}

