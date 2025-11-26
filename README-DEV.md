# 🚀 Guide de développement - Thème OceanWP Intranet

## 📋 Prérequis
- Docker et Docker Compose installés
- Port 8080 (WordPress) et 8081 (phpMyAdmin) disponibles

## 🛠️ Installation et lancement

### 1. Démarrer l'environnement de développement

```bash
docker-compose up -d
```

### 2. Accéder à WordPress

Ouvrez votre navigateur et allez à : **http://localhost:8080**

Lors de la première installation :
- **Langue** : Français
- **Titre du site** : Intranet (ou votre choix)
- **Identifiant** : admin
- **Mot de passe** : (choisissez un mot de passe fort)
- **Email** : votre@email.com

### 3. Activer le thème OceanWP

1. Allez dans **Apparence > Thèmes**
2. Activez le thème **OceanWP**
3. Créez quelques pages et articles de test

### 4. Voir vos modifications en temps réel

✅ **Tous les fichiers du thème sont synchronisés automatiquement !**

Quand vous modifiez :
- `style.css` → Rafraîchissez la page (Ctrl+F5)
- `functions.php` → Rafraîchissez la page
- Templates PHP → Rafraîchissez la page

## 📊 Accès aux outils

| Outil | URL | Identifiants |
|-------|-----|--------------|
| **WordPress** | http://localhost:8080 | admin / (votre mot de passe) |
| **phpMyAdmin** | http://localhost:8081 | root / rootpassword |

## 🎨 Structure du thème

```
intranet/
├── style.css          ← Styles du thème
├── functions.php      ← Fonctions WordPress
├── index.php          ← Template principal
├── header.php         ← En-tête
├── footer.php         ← Pied de page
├── partials/          ← Parties de templates
├── inc/               ← Fonctionnalités PHP
├── assets/            ← CSS, JS, images
└── woocommerce/       ← Templates WooCommerce
```

## 🔧 Commandes utiles

```bash
# Démarrer les conteneurs
docker-compose up -d

# Arrêter les conteneurs
docker-compose down

# Voir les logs
docker-compose logs -f wordpress

# Redémarrer après modifications
docker-compose restart wordpress

# Supprimer tout et recommencer
docker-compose down -v
```

## 💡 Conseils de développement

### Pour modifier le thème :
1. ✏️ Éditez les fichiers dans ce dossier
2. 💾 Sauvegardez
3. 🔄 Rafraîchissez le navigateur (Ctrl+F5)

### Pour ajouter du contenu de test :
1. Allez dans **Articles > Ajouter**
2. Créez 5-10 articles avec images
3. Allez dans **Pages > Ajouter**
4. Créez quelques pages (Accueil, À propos, Contact)

### Pour voir les erreurs PHP :
```bash
docker-compose logs -f wordpress
```

## 🎯 Workflow recommandé

1. **Démarrez Docker** : `docker-compose up -d`
2. **Ouvrez WordPress** : http://localhost:8080
3. **Éditez le thème** dans votre éditeur favori
4. **Rafraîchissez le navigateur** pour voir les changements
5. **Commitez vos modifications** quand c'est prêt

## 🐛 Résolution de problèmes

### Le thème n'apparaît pas ?
```bash
docker-compose restart wordpress
```

### Les modifications ne s'affichent pas ?
- Videz le cache du navigateur (Ctrl+Shift+R ou Ctrl+F5)
- Vérifiez les logs : `docker-compose logs -f wordpress`

### Port 8080 déjà utilisé ?
Modifiez le port dans `docker-compose.yml` :
```yaml
ports:
  - "8090:80"  # Utilisez 8090 au lieu de 8080
```

## 📚 Ressources

- [Documentation OceanWP](https://docs.oceanwp.org/)
- [Codex WordPress](https://codex.wordpress.org/)
- [Documentation Docker](https://docs.docker.com/)
