<?php
require_once __DIR__ . '/../services/MockDataService.php';

// CSRF protection for POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
}

$is_premium = SessionManager::get('is_premium', false);
$generator = new MockDataService();

// Handle Generation Event
if (isset($_POST['generate'])) {
    $gender = $_POST['gender'] ?? 'any';
    $country = $_POST['country'] ?? 'any';
    $age = !empty($_POST['age']) ? (int)$_POST['age'] : null;
    $includeCV = isset($_POST['include_cv']);
    
    $profile = $generator->generateProfile($gender, $country, $age);
    $profile['include_cv'] = $includeCV;
    SessionManager::set('current_profile', $profile);
} elseif (!SessionManager::has('current_profile')) {
    $profile = $generator->generateProfile();
    SessionManager::set('current_profile', $profile);
}

$p = SessionManager::get('current_profile');

// Helper for restricted fields
function show_field($value, $is_premium, $type = 'text') {
    if ($is_premium) return $value;
    
    if ($type == 'phone') return substr($value, 0, 8) . 'XXXX';
    if ($type == 'email') return substr($value, 0, 3) . '****@example.com';
    if ($type == 'cc') return substr($value, 0, 4) . ' **** **** ' . substr($value, -4);
    if ($type == 'cvv') return '***';
    if ($type == 'address') return 'HIDDEN FOR FREE USERS';
    
    return '********';
}
?>

<div class="bg-matrix min-h-screen p-4 rounded-3xl font-mono text-sm relative overflow-hidden">
    
    <!-- Header -->
    <div class="flex justify-between items-center mb-8 relative z-10">
        <?php if (!$is_premium) { ?>
            <a href="app.php?page=upgrade" class="btn btn-warning gap-2 shadow-lg border-none hover:scale-105 transition-transform no-animation">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 011.397 1.398l-.8 1.6V12a1 1 0 01-.293.707l-1 1a1 1 0 00-.293.707V15a1 1 0 01-1 1h-4a1 1 0 01-1-1v-1.586A1 1 0 019 12V8.404l-.8-1.6a1 1 0 011.397-1.398l1.599.8L15.152 4.677 11 3.016V2a1 1 0 01-1-1z" clip-rule="evenodd" /></svg>
                Try PRO
            </a>
        <?php } else { ?>
            <div class="badge badge-success gap-2 p-3 font-bold">PREMIUM ACTIVE</div>
        <?php } ?>
        
        <div class="form-control">
            <label class="label cursor-pointer gap-2">
                <span class="label-text text-white/50">Matrix Mode</span> 
                <input type="checkbox" checked="checked" class="toggle toggle-success" disabled />
            </label>
        </div>
    </div>

    <div class="max-w-4xl mx-auto relative z-10">
        
        <!-- Profile Avatar -->
        <div class="flex flex-col items-center mb-8">
            <div class="w-32 h-32 rounded-full p-1 bg-gradient-to-tr from-cyan-400 to-blue-500 shadow-[0_0_20px_rgba(0,229,255,0.5)] mb-4">
                <img src="https://api.dicebear.com/7.x/personas/svg?seed=<?php echo urlencode($p['name']); ?>" class="rounded-full bg-gray-900 w-full h-full object-cover">
            </div>
            
            <?php if (!$is_premium) { ?>
                <div class="badge badge-outline border-green-500 text-green-500 mb-2">FREE VERSION</div>
            <?php } ?>
            
            <h1 class="text-4xl font-bold text-white mb-6 text-center shadow-black drop-shadow-lg"><?php echo $p['name']; ?></h1>
            
            <?php if ($is_premium): ?>
                <div class="flex gap-3 mb-4 flex-wrap justify-center">
                    <span class="btn btn-primary gap-2 opacity-50 cursor-not-allowed" title="Export coming soon">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Export Profile (Coming Soon)
                    </span>
                </div>
                
                <!-- Profile features note -->
                <div class="bg-black/30 border border-cyan-500/30 rounded-lg p-4 mb-4 max-w-2xl mx-auto">
                    <div class="text-cyan-400 text-sm font-semibold mb-2 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        Premium Profile Includes:
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-xs text-white/70">
                        <div class="flex items-center gap-1"><span class="text-green-500">✓</span> Full identity details</div>
                        <div class="flex items-center gap-1"><span class="text-green-500">✓</span> National ID / NINO</div>
                        <div class="flex items-center gap-1"><span class="text-green-500">✓</span> Financial data</div>
                        <div class="flex items-center gap-1"><span class="text-green-500">✓</span> Address & coordinates</div>
                        <div class="flex items-center gap-1"><span class="text-green-500">✓</span> Work experience & CV</div>
                        <div class="flex items-center gap-1"><span class="text-green-500">✓</span> Skills & education</div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-warning mb-4 text-sm max-w-md mx-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                    </svg>
                    <span>Download package available with Premium</span>
                </div>
            <?php endif; ?>
            
            <!-- Generate Form -->
            <form method="POST" action="" class="w-full max-w-2xl bg-black/50 p-4 rounded-xl border border-white/10 backdrop-blur-sm">
                <?php echo csrf_field(); ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <select name="gender" class="select select-bordered bg-black text-white border-white/20 focus:border-cyan-400 w-full">
                        <option value="any">Any Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                    <select name="country" class="select select-bordered bg-black text-white border-white/20 focus:border-cyan-400 w-full">
                        <option value="any">Any Country</option>
                        <option value="kenya">Kenya</option>
                        <option value="usa">USA</option>
                        <option value="uk">UK</option>
                    </select>
                </div>
                <div class="form-control mb-4">
                    <input type="number" name="age" min="18" max="80" placeholder="Specific Age (Optional)" class="input input-bordered bg-black text-white border-white/20 focus:border-cyan-400 w-full" />
                </div>
                 <div class="form-control mb-4">
                    <label class="cursor-pointer flex items-center gap-2">
                        <input type="checkbox" name="include_cv" value="1" <?php echo ($p['include_cv'] ?? false) ? 'checked' : ''; ?> class="checkbox checkbox-xs checkbox-info" />
                        <span class="label-text text-white">Include CV</span>
                    </label>
                </div>
                 <button type="submit" name="generate" class="btn btn-block btn-info bg-cyan-500 hover:bg-cyan-400 border-none text-black font-bold">
                    Generate New Identity
                </button>
            </form>
        </div>

        <!-- Profile Details Sections -->
        <div class="space-y-6">
            
            <!-- Personal Profile -->
            <div>
                <h3 class="text-xl text-cyan mb-2 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    Personal Profile <span class="text-green-500 text-sm ml-2">(Match 100%)</span>
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label text-cyan-500/70 text-xs">FULL NAME</label>
                        <div class="input-matrix p-3 rounded"><?php echo $p['name']; ?></div>
                    </div>
                    <div class="form-control">
                        <label class="label text-cyan-500/70 text-xs">NATIONALITY</label>
                        <div class="input-matrix p-3 rounded"><?php echo $p['nationality']; ?></div>
                    </div>
                    <div class="form-control">
                        <label class="label text-cyan-500/70 text-xs">EMAIL</label>
                        <div class="input-matrix p-3 rounded"><?php echo show_field($p['email'], $is_premium, 'email'); ?></div>
                    </div>
                    <div class="form-control">
                        <label class="label text-cyan-500/70 text-xs">PHONE</label>
                        <div class="input-matrix p-3 rounded"><?php echo show_field($p['phone'], $is_premium, 'phone'); ?></div>
                    </div>
                    <div class="form-control">
                        <label class="label text-cyan-500/70 text-xs">DATE OF BIRTH</label>
                        <div class="input-matrix p-3 rounded"><?php echo $p['dob']; ?> (Age: <?php echo $p['age']; ?>)</div>
                    </div>
                    <div class="form-control">
                        <label class="label text-cyan-500/70 text-xs">NINO (ID/National Insurance)</label>
                         <?php if ($is_premium) { ?>
                            <div class="input-matrix p-3 rounded tracking-widest"><?php echo $p['nino']; ?></div>
                        <?php } else { ?>
                             <div class="relative group cursor-pointer" onclick="window.location='app.php?page=upgrade'">
                                <div class="input-matrix p-3 rounded blur-sm select-none opacity-50">AB 12 34 56 C</div>
                                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 bg-black/60 text-xs text-white">UNLOCK</div>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="form-control">
                         <label class="label text-cyan-500/70 text-xs">PHYSICAL</label>
                         <div class="input-matrix p-3 rounded"><?php echo $p['height']; ?> / <?php echo $p['blood_type']; ?></div>
                    </div>
                    <div class="form-control">
                         <label class="label text-cyan-500/70 text-xs">USERNAME</label>
                         <div class="input-matrix p-3 rounded"><?php echo strtolower(str_replace(' ', '', $p['name'])) . rand(10,999); ?></div>
                    </div>
                </div>
            </div>

            <div class="divider border-white/10"></div>

            <!-- Address -->
             <div>
                <h3 class="text-xl text-cyan mb-2 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    Address
                </h3>
                <div class="mb-4">
                     <label class="label text-cyan-500/70 text-xs">ADDRESS VALIDITY</label>
                     <div class="w-full bg-gray-900 rounded-full h-10 border border-white/10 flex items-center px-4">
                         <span class="text-green-500 font-bold">100%</span>
                     </div>
                     <span class="text-xs text-gray-500">(Verified by OpenStreetMap)</span>
                </div>
                
                <div class="grid grid-cols-1 gap-4">
                    <div class="form-control">
                        <label class="label text-cyan-500/70 text-xs">STREET</label>
                         <?php if ($is_premium) { ?>
                            <div class="input-matrix p-3 rounded"><?php echo $p['street']; ?></div>
                        <?php } else { ?>
                            <div class="input-matrix p-3 rounded blur-sm select-none opacity-50">123 Hidden Street</div>
                        <?php } ?>
                    </div>
                     <div class="grid grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label text-cyan-500/70 text-xs">CITY/TOWN</label>
                            <div class="input-matrix p-3 rounded"><?php echo $p['city']; ?></div>
                        </div>
                        <div class="form-control">
                            <label class="label text-cyan-500/70 text-xs">ZIP/POSTAL CODE</label>
                            <div class="input-matrix p-3 rounded"><?php echo $p['zip']; ?></div>
                        </div>
                     </div>
                </div>
                 <div class="form-control mt-4">
                        <label class="label text-cyan-500/70 text-xs">COORDINATES</label>
                        <div class="input-matrix p-3 rounded"><?php echo $p['coordinates']; ?></div>
                </div>
                <div class="mt-2 text-white">
                    <button class="btn btn-outline btn-sm gap-2">
                        Map View
                    </button>
                </div>
            </div>

            <div class="divider border-white/10"></div>
            
            <!-- Finance -->
             <div>
                <h3 class="text-xl text-cyan mb-2 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                    Finance & Credit <span class="text-green-500 text-sm ml-2">(Algo Checked Luhn)</span>
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label text-cyan-500/70 text-xs">CARD TYPE</label>
                        <div class="input-matrix p-3 rounded">Visa</div>
                    </div>
                    <div class="form-control">
                        <label class="label text-cyan-500/70 text-xs">CARD NUMBER</label>
                        <div class="input-matrix p-3 rounded tracking-wider font-mono">
                            <?php echo show_field($p['cc_number'], $is_premium, 'cc'); ?>
                        </div>
                    </div>
                    <div class="form-control">
                        <label class="label text-cyan-500/70 text-xs">EXPIRATION</label>
                        <div class="input-matrix p-3 rounded"><?php echo $p['cc_expiry']; ?></div>
                    </div>
                    <div class="form-control">
                        <label class="label text-cyan-500/70 text-xs">CVV</label>
                        <div class="input-matrix p-3 rounded">
                             <?php echo show_field($p['cc_cvv'], $is_premium, 'cvv'); ?>
                        </div>
                    </div>
                </div>
                 <div class="form-control mt-4">
                        <label class="label text-cyan-500/70 text-xs">BALANCE</label>
                        <div class="input-matrix p-3 rounded flex items-center">
                            <span class="text-green-500 mr-1">$</span>
                            <?php echo $p['balance']; ?>
                        </div>
                </div>
            </div>

            <?php if ($is_premium && ($p['include_cv'] ?? false)): ?>
            <div class="divider border-white/10"></div>
            
            <!-- Professional Profile -->
            <div>
                <h3 class="text-xl text-cyan mb-2 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                    Professional Profile
                </h3>
                
                <div class="mb-6">
                    <label class="label text-cyan-500/70 text-xs">CURRENT POSITION</label>
                    <div class="input-matrix p-3 rounded"><?php echo htmlspecialchars($p['job_title']); ?></div>
                </div>
                
                <label class="label text-cyan-500/70 text-xs">WORK EXPERIENCE</label>
                <?php foreach ($p['work_experience'] as $exp): ?>
                <div class="bg-black/30 p-4 rounded-lg mb-3 border border-white/5">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <div class="text-white font-semibold"><?php echo htmlspecialchars($exp['title']); ?></div>
                            <div class="text-cyan-400 text-sm"><?php echo htmlspecialchars($exp['company']); ?></div>
                        </div>
                        <div class="text-gray-400 text-xs"><?php echo $exp['start']; ?> - <?php echo $exp['end']; ?></div>
                    </div>
                    <p class="text-gray-300 text-sm"><?php echo htmlspecialchars($exp['description']); ?></p>
                </div>
                <?php endforeach; ?>
                
                <div class="mt-6">
                    <label class="label text-cyan-500/70 text-xs">EDUCATION</label>
                    <div class="bg-black/30 p-4 rounded-lg border border-white/5">
                        <div class="text-white font-semibold"><?php echo htmlspecialchars($p['education']['degree']); ?></div>
                        <div class="text-cyan-400 text-sm"><?php echo htmlspecialchars($p['education']['major']); ?></div>
                        <div class="text-gray-400 text-sm"><?php echo htmlspecialchars($p['education']['university']); ?> • <?php echo $p['education']['year']; ?></div>
                    </div>
                </div>
                
                <div class="mt-6">
                    <label class="label text-cyan-500/70 text-xs">SKILLS</label>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($p['skills'] as $skill): ?>
                        <span class="badge badge-info gap-2"><?php echo htmlspecialchars($skill); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>

    </div>
</div>
