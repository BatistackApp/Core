#!/usr/bin/env node

/**
 * Script pour envoyer l'email de changelog à changelog@batistack.ovh
 * Utilise l'API GitHub pour récupérer l'artifact et l'envoie par email
 */

import { readFileSync } from 'fs';
import { createTransporter } from './email-config.js';

const CHANGELOG_EMAIL = 'changelog@batistack.ovh';

async function sendChangelogEmail() {
    try {
        // Lire le contenu de l'email généré
        const emailContent = readFileSync('email_content.html', 'utf8');
        
        // Configuration du transporteur email
        const transporter = createTransporter();
        
        // Récupérer la version depuis les variables d'environnement
        const version = process.env.RELEASE_VERSION || 'Unknown';
        
        // Configuration de l'email
        const mailOptions = {
            from: {
                name: 'Batistack Release System',
                address: 'noreply@batistack.ovh'
            },
            to: CHANGELOG_EMAIL,
            subject: `🎉 Nouvelle version Batistack ${version} - Changelog`,
            html: emailContent,
            attachments: [
                {
                    filename: 'batistack-logo.png',
                    path: '.github/assets/logo.png',
                    cid: 'logo'
                }
            ]
        };
        
        // Envoyer l'email
        const info = await transporter.sendMail(mailOptions);
        
        console.log('✅ Email de changelog envoyé avec succès !');
        console.log('📧 Message ID:', info.messageId);
        console.log('📬 Destinataire:', CHANGELOG_EMAIL);
        console.log('📦 Version:', version);
        
        return true;
        
    } catch (error) {
        console.error('❌ Erreur lors de l\'envoi de l\'email:', error);
        process.exit(1);
    }
}

// Exécuter le script si appelé directement
if (import.meta.url === `file://${process.argv[1]}`) {
    sendChangelogEmail();
}

export { sendChangelogEmail };