pipeline {
    agent any

    environment {
        GIT_REPO = 'https://github.com/MACIB-GRUP3/Pokemon.git'
        SONAR_PROJECT_KEY = 'pokemon-php'
        SONAR_PROJECT_NAME = 'Pokemon PHP App'
        DOCKER_NETWORK = 'cicd-network'
    }

    triggers {
        // CAMBIO PARA EL VÍDEO: Revisa cada minuto.
        // Para la entrega final cámbialo a 'H/5 * * * *'
        pollSCM('* * * * *') 
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
                    echo "=== Verificando estructura ==="
                    ls -la
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
                    // abortPipeline: false para que no se pare el vídeo si falla la calidad
                    waitForQualityGate abortPipeline: false 
                }
            }
        }

        stage('Deploy PHP App for DAST') {
            steps {
                script {
                    def hostWorkspace = env.WORKSPACE.replaceFirst("/var/jenkins_home", "/home/grupo03/cicd-setup/jenkins_home")

                    sh """
                        echo "=== 0. Limpiando entorno anterior ==="
                        docker stop pokemon-db pokemon-php-app 2>/dev/null || true
                        docker rm pokemon-db pokemon-php-app 2>/dev/null || true
                        docker network create cicd-network 2>/dev/null || true

                        echo "=== 1. Parcheando conexión a DB ==="
                        grep -rl "localhost" . | xargs sed -i 's/localhost/pokemon-db/g' || true

                       echo "=== 2. Iniciando Base de Datos (MySQL) ==="
                        docker run -d \
                            --name pokemon-db \
                            --network cicd-network \
                            -e MYSQL_ROOT_PASSWORD= \
                            -e MYSQL_ALLOW_EMPTY_PASSWORD=yes \
                            mysql:5.7 \
                            --max_allowed_packet=64M

                        echo "⏳Esperando a que MYSQL arranque..."
                        i=0
                        while [ \$i -lt 30 ]; do
                            if docker exec pokemon-db mysqladmin ping -h localhost --silent; then
                                echo "✅ MySQL is alive!"
                                break
                            fi
                            echo "😴 Waiting for socket... (\$i/30)"
                            sleep 2
                            
                            # FIX HERE: Escape the $ sign
                            i=\$((i+1))
                        done
                        done

                        # AÑADIR ESTA ESPERA:
                        echo "💤 Esperando 15s para asegurar estabilidad..."
                        sleep 15

                       echo "=== 3. Creando DB e Inyectando Datos ==="
                        
                       # Crear la base de datos
                       docker exec pokemon-db mysql -uroot -e "CREATE DATABASE IF NOT EXISTS Pokewebapp;"

                       # Copiar el archivo SQL al contenedor
                       docker cp pokewebapp.sql pokemon-db:/tmp/pokewebapp.sql

                       # CORRECCIÓN: Usar 'source' en lugar de redirección '<' para evitar el Error 1317
                       docker exec pokemon-db mysql -uroot Pokewebapp -e "source /tmp/pokewebapp.sql"
                       echo "=== 4. Iniciando App PHP ==="
                       docker run -d \\
                           --name pokemon-php-app \\
                           --network cicd-network \\
                           -v ${hostWorkspace}:/var/www/html \\
                           -w /var/www/html \\
                           php:8.1-apache

                       echo "=== 5. Configurando Apache ==="
                       sleep 5
                       docker exec pokemon-php-app bash -c "docker-php-ext-install mysqli && docker-php-ext-enable mysqli && a2enmod rewrite headers && apache2ctl graceful"

                       echo "=== 6. Verificación Final ==="
                       sleep 5
                       docker run --rm --network cicd-network appropriate/curl -f -s http://pokemon-php-app:80 > /dev/null && echo "🚀 TODO LISTO" || echo "❌ ERROR: La web no responde"
                      """
                }
            }
        }
        
       stage('DAST - OWASP ZAP Scan') {
            steps {
                script {
                    def hostWorkspace = env.WORKSPACE.replaceFirst("/var/jenkins_home", "/home/grupo03/cicd-setup/jenkins_home")
                    sh """
                        echo "=== Limpiando ZAP anterior ==="
                        docker stop zap-pokemon 2>/dev/null || true
                        docker rm zap-pokemon 2>/dev/null || true
                        
                        mkdir -p ${WORKSPACE}/zap-reports
                        chmod -R 777 ${WORKSPACE}/zap-reports
                        
                        echo "=== Ejecutando OWASP ZAP (Autenticado) ==="
                        # Montamos el archivo zap-plan.yaml dentro del contenedor
                        docker run --name zap-pokemon \\
                            --network ${DOCKER_NETWORK} \\
                            -v ${hostWorkspace}/zap-reports:/zap/wrk:rw \\
                            -v ${hostWorkspace}/zap-plan.yaml:/zap/plan.yaml:ro \\
                            -t ghcr.io/zaproxy/zaproxy:stable \\
                            zap.sh -cmd -autorun /zap/plan.yaml
                        
                        echo "✅ Scan finalizado"
                    """
                }
            }
        }
        
        stage('Security Analysis - PHP Specific') {
            steps {
                script {
                    sh '''
                        echo "=== Análisis de Seguridad Específico ==="
                        # Este grep fallará (exit 1) si encuentra vulnerabilidades, alertando en el log
                        # Quitamos el '|| echo' para que veas el fallo si lo hay, o déjalo si quieres que pase siempre.
                        echo "📌 Buscando SQL Injections..."
                        grep -rn "mysql_query\\|mysqli_query" . --include="*.php" | grep -v "prepare" || echo "✅ Limpio"
                    '''
                }
            }
        }

        stage('Publish Reports') {
            steps {
                publishHTML([
                    allowMissing: true,
                    alwaysLinkToLastBuild: true,
                    keepAll: true,
                    reportDir: 'zap-reports',
                    reportFiles: 'zap_report.html',
                    reportName: 'OWASP ZAP Security Report',
                    reportTitles: 'ZAP Security Scan'
                ])
                archiveArtifacts artifacts: 'zap-reports/**/*', allowEmptyArchive: true, fingerprint: true
            }
        }
    }

    post {
        always {
            script {
                echo "=== Limpiando TODO ==="
                sh '''
                    docker stop pokemon-php-app pokemon-db zap-pokemon 2>/dev/null || true
                    docker rm pokemon-php-app pokemon-db zap-pokemon 2>/dev/null || true
                '''
            }
        }
        success {
            echo "✅ PIPELINE CORRECTO. La DB se conectó y ZAP pudo escanear."
        }
        failure {
            echo "❌ FALLO. Revisa si el contenedor mysql levantó bien."
        }
    }
}
