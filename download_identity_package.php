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
if (!$profile) {
    die('No identity data available');
}

// Create temporary directory for package files
$tempDir = sys_get_temp_dir() . '/identity_' . uniqid();
mkdir($tempDir, 0755, true);

try {
    // 1. Generate profile image
    $imageUrl = 'https://api.dicebear.com/7.x/personas/svg?seed=' . urlencode($profile['name']);
    $imageContent = file_get_contents($imageUrl);
    $imagePath = $tempDir . '/profile_photo.svg';
    file_put_contents($imagePath, $imageContent);
    
    // Also create a PNG version
    $imagePngPath = $tempDir . '/profile_photo.png';
    // For SVG to PNG conversion, we'll just copy the SVG and note it in the readme
    copy($imagePath, $imagePngPath . '.svg');
    
    // 2. Generate detailed identity document (TXT)
    $identityDoc = generateIdentityDocument($profile);
    file_put_contents($tempDir . '/IDENTITY_DETAILS.txt', $identityDoc);
    
    // 3. Generate JSON export
    $jsonData = json_encode($profile, JSON_PRETTY_PRINT);
    file_put_contents($tempDir . '/identity_data.json', $jsonData);
    
    // 4. Generate CV PDF (if CV included)
    if ($profile['include_cv'] ?? false) {
        generateCVPDF($profile, $tempDir . '/CV_' . sanitizeFilename($profile['name']) . '.pdf');
    }
    
    // 5. Generate README file
    $readme = generateReadme($profile);
    file_put_contents($tempDir . '/README.txt', $readme);
    
    // 6. Create ZIP archive
    $zipFilename = 'Identity_Package_' . sanitizeFilename($profile['name']) . '_' . date('Ymd') . '.zip';
    $zipPath = $tempDir . '/' . $zipFilename;
    
    $zip = new ZipArchive();
    if ($zip->open($zipPath, ZipArchive::CREATE) !== TRUE) {
        throw new Exception('Could not create ZIP file');
    }
    
    // Add all files to ZIP
    $files = scandir($tempDir);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..' && $file != $zipFilename) {
            $filePath = $tempDir . '/' . $file;
            if (is_file($filePath)) {
                $zip->addFile($filePath, $file);
            }
        }
    }
    
    $zip->close();
    
    // 7. Download ZIP file
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . $zipFilename . '"');
    header('Content-Length: ' . filesize($zipPath));
    readfile($zipPath);
    
    // 8. Cleanup
    cleanupDirectory($tempDir);
    
} catch (Exception $e) {
    cleanupDirectory($tempDir);
    die('Error generating package: ' . $e->getMessage());
}

function generateIdentityDocument($p) {
    $doc = "═══════════════════════════════════════════════════════════════\n";
    $doc .= "                    IDENTITY PROFILE DOCUMENT                  \n";
    $doc .= "═══════════════════════════════════════════════════════════════\n\n";
    
    $doc .= "Generated: " . date('Y-m-d H:i:s') . "\n";
    $doc .= "Package ID: " . strtoupper(substr(md5($p['name'] . time()), 0, 12)) . "\n\n";
    
    $doc .= "───────────────────────────────────────────────────────────────\n";
    $doc .= "PERSONAL INFORMATION\n";
    $doc .= "───────────────────────────────────────────────────────────────\n\n";
    
    $doc .= "Full Name:        " . $p['name'] . "\n";
    $doc .= "Gender:           " . $p['gender'] . "\n";
    $doc .= "Date of Birth:    " . $p['dob'] . " (Age: " . $p['age'] . ")\n";
    $doc .= "Nationality:      " . $p['nationality'] . "\n";
    $doc .= "National ID:      " . $p['nino'] . "\n";
    $doc .= "Blood Type:       " . $p['blood_type'] . "\n";
    $doc .= "Height:           " . $p['height'] . "\n\n";
    
    $doc .= "───────────────────────────────────────────────────────────────\n";
    $doc .= "CONTACT INFORMATION\n";
    $doc .= "───────────────────────────────────────────────────────────────\n\n";
    
    $doc .= "Email:            " . $p['email'] . "\n";
    $doc .= "Phone:            " . $p['phone'] . "\n";
    $doc .= "Address:          " . $p['street'] . "\n";
    $doc .= "                  " . $p['city'] . ", " . $p['zip'] . "\n";
    $doc .= "                  " . $p['country'] . "\n";
    $doc .= "Coordinates:      " . $p['coordinates'] . "\n\n";
    
    $doc .= "───────────────────────────────────────────────────────────────\n";
    $doc .= "FINANCIAL INFORMATION\n";
    $doc .= "───────────────────────────────────────────────────────────────\n\n";
    
    $doc .= "Card Type:        Visa\n";
    $doc .= "Card Number:      " . $p['cc_number'] . "\n";
    $doc .= "Expiration:       " . $p['cc_expiry'] . "\n";
    $doc .= "CVV:              " . $p['cc_cvv'] . "\n";
    $doc .= "Balance:          $" . $p['balance'] . "\n\n";
    
    if ($p['include_cv'] ?? false) {
        $doc .= "───────────────────────────────────────────────────────────────\n";
        $doc .= "PROFESSIONAL PROFILE\n";
        $doc .= "───────────────────────────────────────────────────────────────\n\n";
        
        $doc .= "Current Position: " . $p['job_title'] . "\n\n";
        
        $doc .= "WORK EXPERIENCE:\n\n";
        foreach ($p['work_experience'] as $exp) {
            $doc .= "  • " . $exp['title'] . "\n";
            $doc .= "    " . $exp['company'] . " | " . $exp['start'] . " - " . $exp['end'] . "\n";
            $doc .= "    " . $exp['description'] . "\n\n";
        }
        
        $doc .= "EDUCATION:\n\n";
        $doc .= "  " . $p['education']['degree'] . " in " . $p['education']['major'] . "\n";
        $doc .= "  " . $p['education']['university'] . " (" . $p['education']['year'] . ")\n\n";
        
        $doc .= "SKILLS:\n\n";
        $doc .= "  " . implode(', ', $p['skills']) . "\n\n";
    }
    
    $doc .= "───────────────────────────────────────────────────────────────\n";
    $doc .= "ONLINE PRESENCE\n";
    $doc .= "───────────────────────────────────────────────────────────────\n\n";
    
    $username = strtolower(str_replace(' ', '', $p['name'])) . rand(10, 999);
    $doc .= "Username:         " . $username . "\n";
    $doc .= "Suggested Email:  " . $p['email'] . "\n";
    $doc .= "Alt Email:        " . strtolower($p['first_name']) . rand(100, 999) . "@" . explode('@', $p['email'])[1] . "\n\n";
    
    $doc .= "═══════════════════════════════════════════════════════════════\n";
    $doc .= "                    END OF DOCUMENT                            \n";
    $doc .= "═══════════════════════════════════════════════════════════════\n";
    
    return $doc;
}

function generateReadme($p) {
    $readme = "IDENTITY PACKAGE - " . strtoupper($p['name']) . "\n";
    $readme .= str_repeat("=", 60) . "\n\n";
    
    $readme .= "Generated: " . date('F j, Y g:i A') . "\n";
    $readme .= "Profile Type: " . ($p['include_cv'] ? 'Professional (with CV)' : 'Standard') . "\n\n";
    
    $readme .= "PACKAGE CONTENTS:\n";
    $readme .= str_repeat("-", 60) . "\n\n";
    
    $readme .= "1. IDENTITY_DETAILS.txt\n";
    $readme .= "   Complete identity information in readable text format\n\n";
    
    $readme .= "2. identity_data.json\n";
    $readme .= "   Machine-readable JSON export of all data\n\n";
    
    $readme .= "3. profile_photo.svg\n";
    $readme .= "   Profile avatar image (SVG format)\n\n";
    
    if ($p['include_cv'] ?? false) {
        $readme .= "4. CV_" . $p['name'] . ".pdf\n";
        $readme .= "   Professional curriculum vitae\n\n";
    }
    
    $readme .= "\nQUICK REFERENCE:\n";
    $readme .= str_repeat("-", 60) . "\n\n";
    
    $readme .= "Name:     " . $p['name'] . "\n";
    $readme .= "Email:    " . $p['email'] . "\n";
    $readme .= "Phone:    " . $p['phone'] . "\n";
    $readme .= "DOB:      " . $p['dob'] . "\n";
    $readme .= "ID:       " . $p['nino'] . "\n\n";
    
    $readme .= "USAGE NOTES:\n";
    $readme .= str_repeat("-", 60) . "\n\n";
    
    $readme .= "• This is a generated identity for testing/demonstration purposes\n";
    $readme .= "• All data is randomly generated and not associated with real persons\n";
    $readme .= "• Credit card numbers pass Luhn validation but are not real accounts\n";
    $readme .= "• Use responsibly and in accordance with applicable laws\n\n";
    
    $readme .= "For support or questions, contact your service provider.\n\n";
    
    $readme .= str_repeat("=", 60) . "\n";
    $readme .= "Generated by IntaSend Identity Generator\n";
    
    return $readme;
}

function generateCVPDF($profile, $outputPath) {
    if (!class_exists('TCPDF')) {
        require_once 'vendor/tecnickcom/tcpdf/tcpdf.php';
    }
    
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator('IntaSend Identity Generator');
    $pdf->SetAuthor($profile['name']);
    $pdf->SetTitle('CV - ' . $profile['name']);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 11);
    
    $html = generateCVHTML($profile);
    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output($outputPath, 'F');
}

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

function sanitizeFilename($filename) {
    $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $filename);
    return $filename;
}

function cleanupDirectory($dir) {
    if (!is_dir($dir)) return;
    
    $files = array_diff(scandir($dir), ['.', '..']);
    foreach ($files as $file) {
        $path = $dir . '/' . $file;
        is_dir($path) ? cleanupDirectory($path) : unlink($path);
    }
    rmdir($dir);
}
