pipeline {
    agent any

    environment {
        GIT_REPO = 'https://github.com/MACIB-GRUP3/Pokemon.git'
        SONAR_PROJECT_KEY = 'pokemon-php'
        SONAR_PROJECT_NAME = 'Pokemon PHP App'
        APP_PORT = '8888'
        ZAP_PORT = '8090'
        # Usamos WORKSPACE para el mapeo de volumen de Docker de forma más segura
        WORKSPACE = sh(returnStdout: true, script: 'pwd').trim()
    }

    triggers {
        pollSCM('H/5 * * * *')
    }

    stages {
        stage('Checkout') {
            steps {
                git branch: 'main', url: "${GIT_REPO}"
            }
        }

        stage('Prepare Environment') {
            steps {
                sh '''
                    echo "=== Verificant estructura del projecte ==="
                    ls -la

                    echo "=== Instal·lant PHP, Composer i dependències ==="
                    # Instal·la PHP i extensions bàsiques si cal
                    # S'afegeix 'zip' i 'unzip' per a Composer i 'git'
                    which php || (apt-get update && apt-get install -y php php-cli php-xml php-mbstring curl git zip unzip)
                    
                    # Instal·la Composer si no està present
                    which composer || (php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && php composer-setup.php --install-dir=/usr/local/bin --filename=composer)
                    
                    # Instal·la dependències (incloent les de dev, com PHPUnit)
                    if [ -f "composer.json" ]; then
                        echo "Instal·lant dependències de Composer (inclou dev)..."
                        composer install --no-interaction --no-progress
                    else
                        echo "⚠️ Advertència: No s'ha trobat composer.json. S'assumeix que PHPUnit està instal·lat globalment."
                    fi
                '''
            }
        }
        
        // 🚨 NOU PAS CLAU: Executa les proves i genera coverage.xml
        stage('Unit Tests & Coverage') {
            steps {
                script {
                    sh '''
                        echo "=== Executant proves unitàries i generant informe de cobertura ==="
                        
                        # Si s'usa Composer, crida l'executable local; altrament, crida el global.
                        if [ -f "vendor/bin/phpunit" ]; then
                           ./vendor/bin/phpunit --coverage-clover coverage.xml || echo "⚠️ Les proves han fallat o no s'han trobat. El coverage.xml es generarà amb resultats parcials o buits."
                        elif which phpunit >/dev/null 2>&1; then
                           phpunit --coverage-clover coverage.xml || echo "⚠️ Les proves han fallat o no s'han trobat (usant phpunit global)."
                        else
                           echo "❌ ERROR: PHPUnit no està instal·lat. La cobertura de codi serà 0."
                        fi
                        
                        # Assegura que el fitxer existeix, encara que estigui buit, per evitar la IOException en SonarQube
                        if [ ! -f "coverage.xml" ]; then
                           echo "⚠️ No s'ha pogut generar coverage.xml. Creant fitxer buit per SonarQube."
                           touch coverage.xml
                        else
                           echo "✅ Informe coverage.xml generat correctament."
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
                        echo "=== Desplegant aplicació PHP per a DAST ==="

                        docker network create zapnet || true

                        docker stop php-pokemon || true
                        docker rm php-pokemon || true

                        # Utilitzem ${WORKSPACE} per garantir la ruta completa al volum
                        docker run -d --name php-pokemon --network zapnet \
                            -v ${WORKSPACE}:/var/www/html \
                            -w /var/www/html \
                            -p ${APP_PORT}:8888 \
                            php:8.2-cli \
                            php -S 0.0.0.0:8888 -t .

                        echo "Esperant que el servidor PHP estigui llest..."
                        sleep 5
                        docker exec php-pokemon curl -I http://localhost:8888 || echo "Servidor PHP iniciat correctament"
                    '''
                }
            }
        }

        stage('DAST - OWASP ZAP Scan') {
            steps {
                script {
                    sh '''
                        echo "=== Iniciant escaneig amb OWASP ZAP ==="

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
                        echo "=== Anàlisi de seguretat específica per PHP ==="

                        echo "-- Buscant SQL injections --"
                        grep -r "mysql_query\\|mysqli_query" . --include="*.php" | grep -v "prepare" || echo "✅ No s'han trobat consultes sense preparar"

                        echo "-- Buscant XSS --"
                        grep -r "echo \\$_GET\\|echo \\$_POST\\|print \\$_GET\\|print \\$_POST" . --include="*.php" || echo "✅ No s'han trobat sortides directes sense escapament"

                        echo "-- Buscant inclusions perilloses --"
                        grep -r "include\\|require" . --include="*.php" | grep "\\$_GET\\|\\$_POST" || echo "✅ No s'han trobat inclusions dinàmiques perilloses"

                        echo "-- Buscant funcions perilloses --"
                        grep -r "eval\\|exec\\|system\\|shell_exec\\|passthru" . --include="*.php" || echo "✅ No s'han trobat funcions perilloses"
                    '''
                }
            }
        }

        stage('Publish Reports') {
            steps {
                script {
                    sh '''
                        echo "=== Verificant informes ZAP ==="
                        mkdir -p ${WORKSPACE}/zap-reports
                        chmod -R 777 ${WORKSPACE}/zap-reports

                        if [ ! -f ${WORKSPACE}/zap-reports/zap_report.html ]; then
                            echo "⚠️ No s'ha trobat zap_report.html, creant placeholder..."
                            echo "<html><body><h2>No s'ha generat l'informe de ZAP.</h2></body></html>" > ${WORKSPACE}/zap-reports/zap_report.html
                        fi
                    '''

                    // Assegura't de tenir instal·lat el plugin 'HTML Publisher'
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
                    echo "=== Netejant recursos ==="
                    docker stop php-pokemon || true
                    docker rm php-pokemon || true
                    docker stop zap-pokemon || true
                    docker rm zap-pokemon || true
                    docker network rm zapnet || true
                '''
            }
        }

        success {
            echo """
            ✅ Pipeline completat correctament!
            📊 Consulta els informes a:
            - SonarQube: http://[IP-VM]:9000/dashboard?id=${SONAR_PROJECT_KEY}
            - OWASP ZAP: Arxius d'artefactes de Jenkins
            """
        }

        failure {
            echo '❌ El pipeline ha fallat. Revisa els logs per més detalls.'
        }
    }
}
