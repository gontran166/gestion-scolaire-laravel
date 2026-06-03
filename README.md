# Application de Gestion Scolaire (CP1 – CM2)

Ce projet vise à fournir une solution complète pour la gestion d'un établissement d'enseignement primaire (du CP1 au CM2). Elle permet un suivi rigoureux sur les plans administratif, financier et pédagogique.

---

             👥 Membres du binôme

Nom et prénom : _OUATTARA karim_
Adresse email : ouattarakarim@gmail.com
Lien GitHub :

Nom et prénom : _NOMBO W Gontran_
Adresse email : gontrannombo180@gmail.com
LienGitHub :https://github.com/gontran166/gestion-scolaire-laravel

            🚀 Fonctionnalités

📊 Tableau de Bord Analytique
Admin/Directeur :
• Nombre total des élèves
• Situation financière globale
• Menu des accès rapide
• Statistique sur les frais collectés vs frais attendus ;
• Liste des élèves en retard de paiement (impayés) ;

👥 Gestion Utilisateurs & Authentification
• Rôles multiples : Directeur, Enseignant
• Authentification sécurisée en fonction du rôle (directeur ou enseignant)

💰 Gestion administrative & financière
• Gestion des frais et échéances
• États : payé/partiel/impayé
• Inscription des élèves avec les informations de base et photo
• Configuration des classes et définition des frais de scolarité par classe ;
• Enregistrement des versements effectués par les parents ;
• Génération d’un reçu de paiement (format PDF) et calcul du reste à payer

🏫 Gestion Pédagogique
• Années scolaires avec gestion des trimestres
• Niveaux éducatifs : Enseignement primaire (CP1-CM2)
• Saisie des notes par matière pour chaque élève ( l’enseignant responsable de la classe uniquement)
• Calcul automatique des moyennes trimestrielles.
• Tableau de bord affichant le classement des élèves par classe.

⚙️ Technologies utilisées
• Backend : PHP 8.2, Laravel 12
• Frontend : HTML5, Bootstrap, blade
• Framework : laravel
• Base de données : MySQL
• Outils : Composer, Artisan, GitHub, DomPDF (pour reçus PDF)

Relations Eloquent Avancées
• OneToMany :
users → classes ( un enseignant gère une ou plusieurs classes )
classes → eleves : une classe contient plusieurs élèves
classes → matieres : chaque classe a ses propres matières (les matières du CP1 diffèrent du CM2)
eleves → paiements : un élève peut avoir plusieurs versements au fil de l'année ( gestion de paiement en plusieurs tranche)
eleves → notes : un élève reçoit des notes dans différentes matières.
matieres → notes : une note par trimestre dans une matière.
• Soft Deletes : Préservation historique

🔒 Sécurité
Mesures Implémentées
• CSRF Protection sur tous les formulaires
• XSS Prevention avec validation stricte
• SQL Injection protection via Éloquent

INSTALLATION

# 1. Cloner le projet

git clone https://github.com/your-repo/edumaster.git
cd dossier_ecol

# 2. Installer les dépendances

composer install
npm install

# 3. Configuration environnement

cp .env.example .env
php artisan key:generate

# 4. Configuration base de données

# Éditer .env avec vos paramètres DB

# 5. Migrations et seeders

php artisan db:seed
php artisan migrate:fresh --seed

# 6. Permissions et stockage

php artisan storage:link
php artisan permission:cache-reset

# 7. Compilation assets

npm run build

# 8. Lancement serveur

php artisan serve

configuration de base de donnee
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=school
DB_USERNAME=root
DB_PASSWORD=password

    👤 Comptes par Défaut

Après php artisan db:seed :

Rôle Email Mot de passe
Directeur admin@ecole.bf password
Enseignant enseignant@ecole.bf password
