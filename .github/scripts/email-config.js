#!/usr/bin/env node

/**
 * Configuration email pour l'envoi des notifications de changelog
 */

import nodemailer from 'nodemailer';

/**
 * Créer un transporteur email configuré
 * @returns {nodemailer.Transporter}
 */
export function createTransporter() {
    // Configuration SMTP (à adapter selon votre fournisseur)
    const config = {
        host: process.env.SMTP_HOST || 'smtp.batistack.ovh',
        port: parseInt(process.env.SMTP_PORT) || 587,
        secure: process.env.SMTP_SECURE === 'true' || false,
        auth: {
            user: process.env.SMTP_USER || 'noreply@batistack.ovh',
            pass: process.env.SMTP_PASSWORD
        },
        tls: {
            rejectUnauthorized: false
        }
    };
    
    return nodemailer.createTransporter(config);
}

/**
 * Valider la configuration email
 * @returns {Promise<boolean>}
 */
export async function validateEmailConfig() {
    try {
        const transporter = createTransporter();
        await transporter.verify();
        console.log('✅ Configuration email valide');
        return true;
    } catch (error) {
        console.error('❌ Configuration email invalide:', error.message);
        return false;
    }
}