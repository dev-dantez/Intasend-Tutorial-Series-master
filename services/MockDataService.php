<?php

class MockDataService {
    
    private $maleFirstNames = ['James', 'John', 'Robert', 'Michael', 'William', 'David', 'Richard', 'Joseph', 'Thomas', 'Charles', 'Daniel', 'Matthew', 'Anthony', 'Mark', 'Donald', 'Steven', 'Paul', 'Andrew', 'Joshua', 'Kenneth'];
    private $femaleFirstNames = ['Mary', 'Patricia', 'Jennifer', 'Linda', 'Elizabeth', 'Barbara', 'Susan', 'Jessica', 'Sarah', 'Karen', 'Nancy', 'Lisa', 'Betty', 'Margaret', 'Sandra', 'Ashley', 'Kimberly', 'Emily', 'Donna', 'Michelle'];
    private $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez', 'Hernandez', 'Lopez', 'Gonzalez', 'Wilson', 'Anderson', 'Thomas', 'Taylor', 'Moore', 'Jackson', 'Martin', 'Lee', 'Thompson', 'White', 'Harris', 'Clark'];
    
    private $kenyanFirstNames = ['Kamau', 'Wanjiru', 'Otieno', 'Akinyi', 'Mwangi', 'Njeri', 'Omondi', 'Adhiambo', 'Kariuki', 'Wambui'];
    private $kenyanLastNames = ['Kimani', 'Ochieng', 'Mutua', 'Wanjiku', 'Kipchoge', 'Chebet', 'Maina', 'Njoroge', 'Onyango', 'Wafula'];
    
    private $domains = ['gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com', 'protonmail.com', 'icloud.com'];
    private $usStreets = ['Main St', 'Oak Ave', 'Maple Dr', 'Washington Blvd', 'Park Ln', 'Cedar Rd', 'Elm St', 'Lake View Dr', 'Sunset Blvd', 'Broadway'];
    private $usCities = ['New York', 'Los Angeles', 'Chicago', 'Houston', 'Phoenix', 'Philadelphia', 'San Antonio', 'San Diego', 'Dallas', 'San Jose'];
    private $ukCities = ['London', 'Manchester', 'Birmingham', 'Leeds', 'Glasgow', 'Liverpool', 'Newcastle', 'Sheffield', 'Bristol', 'Edinburgh'];
    private $kenyanCities = ['Nairobi', 'Mombasa', 'Kisumu', 'Nakuru', 'Eldoret', 'Thika', 'Malindi', 'Kitale', 'Garissa', 'Kakamega'];
    
    private $jobTitles = [
        'Software Engineer', 'Data Analyst', 'Product Manager', 'UX Designer', 'Marketing Manager',
        'Sales Executive', 'Financial Analyst', 'HR Manager', 'Operations Manager', 'Business Analyst',
        'Project Manager', 'Accountant', 'Graphic Designer', 'Content Writer', 'Customer Success Manager'
    ];
    
    private $skills = [
        'Python', 'JavaScript', 'SQL', 'Project Management', 'Data Analysis', 'Excel', 'Communication',
        'Leadership', 'Problem Solving', 'Team Collaboration', 'Strategic Planning', 'Customer Service',
        'Marketing', 'Sales', 'Financial Modeling', 'Adobe Creative Suite', 'Agile', 'Scrum'
    ];
    
    private $companies = [
        'Tech Solutions Inc', 'Global Innovations Ltd', 'Digital Dynamics', 'Enterprise Systems Corp',
        'Creative Agency Group', 'Financial Services Ltd', 'Consulting Partners', 'Marketing Pros',
        'Data Analytics Co', 'Software Development Ltd'
    ];

    private function random($array) {
        return $array[array_rand($array)];
    }

    private function generateEmail($first, $last) {
        return strtolower($first . '.' . $last . rand(10, 99) . '@' . $this->random($this->domains));
    }

    private function generatePhoneNumber($country) {
        if ($country === 'kenya') {
            return '+2547' . rand(10, 99) . rand(100, 999) . rand(1000, 9999);
        } elseif ($country === 'uk') {
            return '+447' . rand(100, 999) . rand(100000, 999999);
        } else { // USA
            return '+1' . rand(200, 999) . rand(100, 999) . rand(1000, 9999);
        }
    }

    private function generateDateOfBirth($age = null) {
        if ($age) {
            $year = date('Y') - $age;
            $month = rand(1, 12);
            $day = rand(1, 28);
            return sprintf('%04d-%02d-%02d', $year, $month, $day);
        }
        $timestamp = mt_rand(strtotime('1970-01-01'), strtotime('2003-01-01'));
        return date('Y-m-d', $timestamp);
    }

    private function generateAddress($country) {
        if ($country === 'kenya') {
            return [
                'street' => rand(1, 999) . ' ' . $this->random(['Kenyatta Ave', 'Moi Ave', 'Uhuru Highway', 'Ngong Road', 'Thika Road']),
                'city' => $this->random($this->kenyanCities),
                'zip' => rand(10000, 99999),
                'country' => 'Kenya'
            ];
        } elseif ($country === 'uk') {
            return [
                'street' => rand(1, 99) . ' ' . $this->random(['High Street', 'Church Road', 'Station Road', 'Victoria Street', 'King Street']),
                'city' => $this->random($this->ukCities),
                'zip' => strtoupper(chr(rand(65, 90)) . chr(rand(65, 90)) . rand(1, 9) . ' ' . rand(1, 9) . chr(rand(65, 90)) . chr(rand(65, 90))),
                'country' => 'United Kingdom'
            ];
        } else { // USA
            return [
                'street' => rand(100, 9999) . ' ' . $this->random($this->usStreets),
                'city' => $this->random($this->usCities),
                'zip' => rand(10000, 99999),
                'country' => 'United States'
            ];
        }
    }

    private function generateNationalID($country) {
        if ($country === 'kenya') {
            return rand(10000000, 39999999);
        } elseif ($country === 'uk') {
            return sprintf("%s%s %02d %02d %02d %s", 
                chr(rand(65, 90)), chr(rand(65, 90)), 
                rand(10, 99), rand(10, 99), rand(10, 99), 
                chr(rand(65, 68)));
        } else { // USA SSN
            return sprintf("%03d-%02d-%04d", rand(100, 899), rand(10, 99), rand(1000, 9999));
        }
    }

    private function generateCoordinates($country) {
        if ($country === 'kenya') {
            return sprintf("%.6f, %.6f", rand(-4500, 500) / 100, rand(33500, 41500) / 100);
        } elseif ($country === 'uk') {
            return sprintf("%.6f, %.6f", rand(49500, 60500) / 100, rand(-8000, 2000) / 100);
        } else { // USA
            return sprintf("%.6f, %.6f", rand(25000, 49000) / 100, rand(-125000, -65000) / 100);
        }
    }

    private function generateCreditCard() {
        // Generate valid Luhn algorithm credit card
        $prefix = '4'; // Visa
        $ccNumber = $prefix;
        for ($i = 0; $i < 14; $i++) {
            $ccNumber .= rand(0, 9);
        }
        
        // Calculate Luhn check digit
        $sum = 0;
        $alt = false;
        for ($i = strlen($ccNumber) - 1; $i >= 0; $i--) {
            $n = (int)$ccNumber[$i];
            if ($alt) {
                $n *= 2;
                if ($n > 9) $n -= 9;
            }
            $sum += $n;
            $alt = !$alt;
        }
        $checkDigit = (10 - ($sum % 10)) % 10;
        $ccNumber .= $checkDigit;
        
        return substr($ccNumber, 0, 4) . ' ' . substr($ccNumber, 4, 4) . ' ' . substr($ccNumber, 8, 4) . ' ' . substr($ccNumber, 12, 4);
    }
    
    private function generateMoney() {
        return number_format(rand(1000, 50000) + (rand(0, 99) / 100), 2);
    }
    
    private function generateWorkExperience() {
        $experiences = [];
        $numJobs = rand(2, 4);
        $currentYear = date('Y');
        
        for ($i = 0; $i < $numJobs; $i++) {
            $yearsAgo = $i * rand(2, 4);
            $duration = rand(1, 3);
            $endYear = $currentYear - $yearsAgo;
            $startYear = $endYear - $duration;
            
            $experiences[] = [
                'title' => $this->random($this->jobTitles),
                'company' => $this->random($this->companies),
                'start' => $startYear,
                'end' => ($i === 0) ? 'Present' : $endYear,
                'description' => $this->generateJobDescription()
            ];
        }
        
        return $experiences;
    }
    
    private function generateJobDescription() {
        $descriptions = [
            'Led cross-functional teams to deliver high-impact projects on time and within budget',
            'Developed and implemented strategic initiatives that increased efficiency by 30%',
            'Managed client relationships and consistently exceeded quarterly targets',
            'Collaborated with stakeholders to identify and resolve complex business challenges',
            'Streamlined processes resulting in significant cost savings and improved productivity'
        ];
        return $this->random($descriptions);
    }
    
    private function generateEducation() {
        $degrees = ['Bachelor of Science', 'Bachelor of Arts', 'Master of Business Administration', 'Bachelor of Engineering'];
        $majors = ['Computer Science', 'Business Administration', 'Marketing', 'Finance', 'Engineering', 'Economics'];
        $universities = ['State University', 'Tech Institute', 'Business School', 'National University', 'College of Arts'];
        
        $gradYear = date('Y') - rand(5, 15);
        
        return [
            'degree' => $this->random($degrees),
            'major' => $this->random($majors),
            'university' => $this->random($universities),
            'year' => $gradYear
        ];
    }

    public function generateProfile($gender = 'any', $country = 'any', $age = null) {
        // Determine gender
        if ($gender === 'any') {
            $gender = rand(0, 1) ? 'male' : 'female';
        }
        
        // Determine country
        if ($country === 'any') {
            $country = $this->random(['usa', 'uk', 'kenya']);
        }
        
        // Generate name based on country and gender
        if ($country === 'kenya') {
            $firstName = $this->random($this->kenyanFirstNames);
            $lastName = $this->random($this->kenyanLastNames);
            $nationality = 'Kenyan';
        } else {
            $firstName = $gender === 'male' ? $this->random($this->maleFirstNames) : $this->random($this->femaleFirstNames);
            $lastName = $this->random($this->lastNames);
            $nationality = $country === 'uk' ? 'British' : 'American';
        }
        
        $address = $this->generateAddress($country);
        $education = $this->generateEducation();
        $workExperience = $this->generateWorkExperience();
        $selectedSkills = array_rand(array_flip($this->skills), rand(5, 8));
        
        return [
            'gender' => ucfirst($gender),
            'name' => $firstName . ' ' . $lastName,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $this->generateEmail($firstName, $lastName),
            'phone' => $this->generatePhoneNumber($country),
            'street' => $address['street'],
            'city' => $address['city'],
            'zip' => $address['zip'],
            'country' => $address['country'],
            'nationality' => $nationality,
            'dob' => $this->generateDateOfBirth($age),
            'age' => $age ?? (date('Y') - (int)substr($this->generateDateOfBirth(), 0, 4)),
            'nino' => $this->generateNationalID($country),
            'coordinates' => $this->generateCoordinates($country),
            'cc_number' => $this->generateCreditCard(),
            'cc_expiry' => sprintf("%02d/%02d", rand(1, 12), rand(25, 30)),
            'cc_cvv' => rand(100, 999),
            'balance' => $this->generateMoney(),
            'blood_type' => $this->random(['A+', 'O+', 'B+', 'AB+', 'A-', 'O-', 'B-', 'AB-']),
            'height' => rand(160, 190) . 'cm',
            'education' => $education,
            'work_experience' => $workExperience,
            'skills' => $selectedSkills,
            'job_title' => $workExperience[0]['title']
        ];
    }
}
