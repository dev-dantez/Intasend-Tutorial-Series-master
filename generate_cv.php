<?php
require_once 'config/config.php';
require_once 'config/session.php';
require_once 'includes/functions.php';
require_once 'middleware/auth.php';

SessionManager::init();
require_auth();

// Check if premium
if (!SessionManager::get('is_premium', false)) {
    die('Premium subscription required');
}

// Get profile from session
$profile = SessionManager::get('current_profile');
if (!$profile || !($profile['include_cv'] ?? false)) {
    die('No CV data available');
}

// Check if TCPDF is available
if (!class_exists('TCPDF')) {
    // Fallback to simple HTML CV if TCPDF not installed
    generateHTMLCV($profile);
    exit;
}

// Generate PDF CV using TCPDF
require_once 'vendor/tecnickcom/tcpdf/tcpdf.php';

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('IntaSend Identity Generator');
$pdf->SetAuthor($profile['name']);
$pdf->SetTitle('CV - ' . $profile['name']);

// Remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', '', 11);

// Build CV content
$html = generateCVHTML($profile);

// Output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// Close and output PDF document
$pdf->Output('CV_' . str_replace(' ', '_', $profile['name']) . '.pdf', 'D');

function generateCVHTML($p) {
    $html = '
    <style>
        h1 { color: #2563eb; font-size: 24px; margin-bottom: 5px; }
        h2 { color: #1e40af; font-size: 16px; margin-top: 15px; margin-bottom: 8px; border-bottom: 2px solid #2563eb; padding-bottom: 3px; }
        h3 { color: #374151; font-size: 14px; margin-bottom: 3px; }
        p { margin: 3px 0; font-size: 11px; }
        .contact { color: #6b7280; font-size: 10px; }
        .section { margin-bottom: 15px; }
        .job-title { font-weight: bold; color: #1f2937; }
        .company { color: #4b5563; font-style: italic; }
        .date { color: #9ca3af; font-size: 10px; }
        ul { margin: 5px 0; padding-left: 20px; }
        li { margin: 3px 0; font-size: 11px; }
    </style>
    
    <h1>' . htmlspecialchars($p['name']) . '</h1>
    <p class="contact">' . htmlspecialchars($p['email']) . ' | ' . htmlspecialchars($p['phone']) . '</p>
    <p class="contact">' . htmlspecialchars($p['street']) . ', ' . htmlspecialchars($p['city']) . ', ' . htmlspecialchars($p['zip']) . '</p>
    
    <div class="section">
        <h2>Professional Summary</h2>
        <p>Experienced ' . htmlspecialchars($p['job_title']) . ' with a proven track record of delivering high-quality results. 
        Strong background in ' . implode(', ', array_slice($p['skills'], 0, 3)) . '. 
        Passionate about driving innovation and achieving organizational goals through strategic thinking and collaborative teamwork.</p>
    </div>
    
    <div class="section">
        <h2>Work Experience</h2>';
    
    foreach ($p['work_experience'] as $exp) {
        $html .= '
        <div style="margin-bottom: 10px;">
            <p class="job-title">' . htmlspecialchars($exp['title']) . '</p>
            <p class="company">' . htmlspecialchars($exp['company']) . ' | <span class="date">' . $exp['start'] . ' - ' . $exp['end'] . '</span></p>
            <ul>
                <li>' . htmlspecialchars($exp['description']) . '</li>
            </ul>
        </div>';
    }
    
    $html .= '
    </div>
    
    <div class="section">
        <h2>Education</h2>
        <p class="job-title">' . htmlspecialchars($p['education']['degree']) . ' in ' . htmlspecialchars($p['education']['major']) . '</p>
        <p class="company">' . htmlspecialchars($p['education']['university']) . ' | <span class="date">' . $p['education']['year'] . '</span></p>
    </div>
    
    <div class="section">
        <h2>Skills</h2>
        <p>' . implode(' • ', array_map('htmlspecialchars', $p['skills'])) . '</p>
    </div>
    
    <div class="section">
        <h2>Personal Information</h2>
        <p><strong>Date of Birth:</strong> ' . htmlspecialchars($p['dob']) . '</p>
        <p><strong>Nationality:</strong> ' . htmlspecialchars($p['nationality']) . '</p>
        <p><strong>National ID:</strong> ' . htmlspecialchars($p['nino']) . '</p>
    </div>';
    
    return $html;
}

function generateHTMLCV($p) {
    // Fallback HTML version if TCPDF not available
    header('Content-Type: text/html; charset=utf-8');
    header('Content-Disposition: attachment; filename="CV_' . str_replace(' ', '_', $p['name']) . '.html"');
    
    echo '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>CV - ' . htmlspecialchars($p['name']) . '</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 800px; margin: 40px auto; padding: 20px; line-height: 1.6; }
            h1 { color: #2563eb; border-bottom: 3px solid #2563eb; padding-bottom: 10px; }
            h2 { color: #1e40af; margin-top: 30px; border-bottom: 2px solid #93c5fd; padding-bottom: 5px; }
            .contact { color: #6b7280; margin-bottom: 20px; }
            .job { margin-bottom: 20px; }
            .job-title { font-weight: bold; font-size: 1.1em; }
            .company { color: #4b5563; font-style: italic; }
            .date { color: #9ca3af; }
            .skills { display: flex; flex-wrap: wrap; gap: 10px; }
            .skill { background: #dbeafe; padding: 5px 15px; border-radius: 15px; }
        </style>
    </head>
    <body>';
    
    echo generateCVHTML($p);
    
    echo '</body></html>';
}
