pipeline {
    agent any

    environment {
        GIT_REPO = 'https://github.com/MACIB-GRUP3/Pokemon.git'
        SONAR_PROJECT_KEY = 'pokemon-php'
        SONAR_PROJECT_NAME = 'Pokemon PHP App'
        APP_PORT = '8888'
        ZAP_PORT = '8090'
        WORKSPACE = sh(returnStdout: true, script: 'pwd').trim()
    }

    triggers {
        pollSCM('H/5 * * * *')
    }

    stages {
        stage('Checkout') {
            steps {
                sh 'git config --global http.sslVerify false'
                git branch: 'main', url: "${GIT_REPO}"
            }
        }

        stage('Prepare Environment') {
            steps {
                sh '''
                    apt-get update
                    apt-get install -y --no-install-recommends \
                        php-cli \
                        php-xml \
                        php-mbstring \
                        curl \
                        git \
                        zip \
                        unzip \
                        ca-certificates
                    
                    which composer || (php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && php composer-setup.php --install-dir=/usr/local/bin --filename=composer)
                    
                    if [ -f "composer.json" ]; then
                        composer install --no-interaction --no-progress
                    fi
                '''
            }
        }
        
        stage('Unit Tests & Coverage') {
            steps {
                script {
                    sh '''
                        if [ -f "vendor/bin/phpunit" ]; then
                           ./vendor/bin/phpunit --coverage-clover coverage.xml || true
                        elif which phpunit >/dev/null 2>&1; then
                           phpunit --coverage-clover coverage.xml || true
                        fi
                        
                        if [ ! -f "coverage.xml" ]; then
                           touch coverage.xml
                        fi
                    '''
                }
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
                        docker network create zapnet || true

                        docker stop php-pokemon || true
                        docker rm php-pokemon || true

                        docker run -d --name php-pokemon --network zapnet \
                            -v ${WORKSPACE}:/var/www/html \
                            -w /var/www/html \
                            -p ${APP_PORT}:8888 \
                            php:8.2-cli \
                            php -S 0.0.0.0:8888 -t .

                        sleep 5
                        docker exec php-pokemon curl -I http://localhost:8888 || true
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
                        chmod -R 777 ${WORKSPACE}/zap-reports

                        if ! docker image inspect ghcr.io/zaproxy/zaproxy:weekly >/dev/null 2>&1; then
                            docker pull ghcr.io/zaproxy/zaproxy:weekly
                        fi

                        docker run --user root --name zap-pokemon --network zapnet \
                            -v ${WORKSPACE}/zap-reports:/zap/wrk:rw \
                            -t ghcr.io/zaproxy/zaproxy:weekly \
                            zap-baseline.py -t http://php-pokemon:8888 -r zap_report.html -I
                    '''
                }
            }
        }

        stage('Security Analysis - PHP Specific') {
            steps {
                script {
                    sh '''
                        grep -r "mysql_query\\|mysqli_query" . --include="*.php" | grep -v "prepare" || true
                        grep -r "echo \\$_GET\\|echo \\$_POST\\|print \\$_GET\\|print \\$_POST" . --include="*.php" || true
                        grep -r "include\\|require" . --include="*.php" | grep "\\$_GET\\|\\$_POST" || true
                        grep -r "eval\\|exec\\|system\\|shell_exec\\|passthru" . --include="*.php" || true
                    '''
                }
            }
        }

        stage('Publish Reports') {
            steps {
                script {
                    sh '''
                        mkdir -p ${WORKSPACE}/zap-reports
                        chmod -R 777 ${WORKSPACE}/zap-reports

                        if [ ! -f ${WORKSPACE}/zap-reports/zap_report.html ]; then
                            echo "<html><body><h2>No s'ha generat l'informe de ZAP.</h2></body></html>" > ${WORKSPACE}/zap-reports/zap_report.html
                        fi
                    '''

                    publishHTML([
                        allowMissing: true,
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
    }

    post {
        always {
            script {
                sh '''
                    docker stop php-pokemon || true
                    docker rm php-pokemon || true
                    docker stop zap-pokemon || true
                    docker rm zap-pokemon || true
                    docker network rm zapnet || true
                '''
            }
        }

        success {
            echo "Pipeline completat correctament!"
        }

        failure {
            echo 'El pipeline ha fallat. Revisa els logs per més detalls.'
        }
    }
}
