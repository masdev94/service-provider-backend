<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\ServiceProvider;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all categories
        $categories = Category::all();

        // First, ensure the logos directory exists
        if (!Storage::disk('public')->exists('logos')) {
            Storage::disk('public')->makeDirectory('logos');
        }

        // Path where seed logo images are stored
        $seedLogosPath = storage_path('app/public/seed-logos');

        // Check if seed-logos directory exists
        if (!File::exists($seedLogosPath)) {
            $this->command->error('Seed logos directory not found! Please create storage/app/public/seed-logos directory with logo images.');
            return;
        }

        // Get all logo files from the seed directory
        $logoFiles = File::files($seedLogosPath);

        if (count($logoFiles) === 0) {
            $this->command->error('No logo files found in seed-logos directory!');
            return;
        }

        // Predefined sample service providers by category
        $serviceProviders = [
            'Technology' => [
                [
                    'name' => 'TechSolutions Inc.',
                    'short_description' => 'Enterprise software solutions for digital transformation',
                    'description' => 'A leading provider of enterprise software solutions specializing in cloud migration, digital transformation, and custom application development. With over 15 years of experience serving Fortune 500 companies, TechSolutions delivers reliable and scalable technology infrastructure.',
                ],
                [
                    'name' => 'DataStream Analytics',
                    'short_description' => 'Big data processing and analytics services',
                    'description' => 'DataStream Analytics offers cutting-edge big data processing and analytics services. Our proprietary algorithms help businesses extract meaningful insights from massive datasets, enabling data-driven decision making across all organizational levels.',
                ],
                [
                    'name' => 'CloudNative Systems',
                    'short_description' => 'Cloud infrastructure and containerization experts',
                    'description' => 'Specialists in containerization, microservices architecture, and Kubernetes orchestration. We help organizations modernize their application infrastructure with cloud-native approaches, improving scalability and reducing operational overhead.',
                ],
            ],
            'Healthcare' => [
                [
                    'name' => 'MediCare Solutions',
                    'short_description' => 'Healthcare management systems and telemedicine',
                    'description' => 'Comprehensive healthcare information systems that streamline patient management, billing, and telemedicine capabilities. Our integrated solutions help healthcare providers deliver better patient care while optimizing operational efficiency.',
                ],
                [
                    'name' => 'Wellness Partners',
                    'short_description' => 'Holistic healthcare and preventive medicine',
                    'description' => 'Wellness Partners focuses on preventive healthcare services, offering comprehensive wellness programs, nutrition counseling, fitness regimens, and mental health support to promote overall wellbeing and reduce healthcare costs.',
                ],
            ],
            'Finance' => [
                [
                    'name' => 'Secure Investments Ltd',
                    'short_description' => 'Wealth management and financial planning',
                    'description' => 'Secure Investments provides personalized wealth management services, retirement planning, and investment strategies. Our team of certified financial advisors works closely with clients to achieve long-term financial goals with risk-optimized approaches.',
                ],
                [
                    'name' => 'FinTech Innovations',
                    'short_description' => 'Digital banking and payment solutions',
                    'description' => 'At the forefront of financial technology, offering digital payment processing, mobile banking platforms, and blockchain-based financial services that are transforming how consumers and businesses handle transactions.',
                ],
            ],
            'Education' => [
                [
                    'name' => 'Learning Pathways',
                    'short_description' => 'Online education and skill development',
                    'description' => 'Personalized learning experiences through our adaptive online platform. We offer courses in high-demand skills, professional certifications, and continuous education programs designed to meet the needs of modern learners.',
                ],
                [
                    'name' => 'EduConsult Group',
                    'short_description' => 'Educational consulting and institutional development',
                    'description' => 'Specialized consulting services for educational institutions looking to enhance curriculum design, improve teaching methodologies, implement education technology, and develop strategic growth plans.',
                ],
            ],
        ];

        // Keep track of used logo files to avoid duplicates
        $usedLogoFiles = [];

        // Seed predefined service providers first
        $totalCreated = 0;

        foreach ($serviceProviders as $categoryName => $providers) {
            $category = $categories->where('name', $categoryName)->first();

            if (!$category) {
                continue;
            }

            foreach ($providers as $providerData) {
                // Get a random logo file that hasn't been used yet
                $availableLogos = array_diff($logoFiles, $usedLogoFiles);

                if (count($availableLogos) === 0) {
                    // If all logos have been used, reset the used array
                    $usedLogoFiles = [];
                    $availableLogos = $logoFiles;
                }

                $logoFile = $availableLogos[array_rand($availableLogos)];
                $usedLogoFiles[] = $logoFile;

                // Copy the logo file to the public storage
                $filename = Str::random(10) . '_' . basename($logoFile);
                $targetPath = 'logos/' . $filename;
                Storage::disk('public')->put(
                    $targetPath,
                    file_get_contents($logoFile->getPathname())
                );

                // Create the service provider with logo
                ServiceProvider::create([
                    'name' => $providerData['name'],
                    'slug' => Str::slug($providerData['name']),
                    'short_description' => $providerData['short_description'],
                    'description' => $providerData['description'],
                    'logo' => Storage::url($targetPath),
                    'category_id' => $category->id,
                ]);

                $totalCreated++;
            }
        }

        // Create additional random service providers to fill other categories
        // Generate 2-3 service providers per remaining category
        foreach ($categories as $category) {
            // Skip categories that already have predefined providers
            if (array_key_exists($category->name, $serviceProviders)) {
                continue;
            }

            // Generate 2-3 providers per category
            $providersToCreate = rand(2, 3);

            for ($i = 0; $i < $providersToCreate; $i++) {
                // Get a random logo file that hasn't been used yet
                $availableLogos = array_diff($logoFiles, $usedLogoFiles);

                if (count($availableLogos) === 0) {
                    // If all logos have been used, reset the used array
                    $usedLogoFiles = [];
                    $availableLogos = $logoFiles;
                }

                $logoFile = $availableLogos[array_rand($availableLogos)];
                $usedLogoFiles[] = $logoFile;

                // Copy the logo file to the public storage
                $filename = Str::random(10) . '_' . basename($logoFile);
                $targetPath = 'logos/' . $filename;
                Storage::disk('public')->put(
                    $targetPath,
                    file_get_contents($logoFile->getPathname())
                );

                // Create a name based on the category
                $name = $this->generateBusinessName($category->name);

                // Create the service provider with logo
                ServiceProvider::create([
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'short_description' => $this->generateShortDescription($category->name),
                    'description' => $this->generateDescription($category->name),
                    'logo' => Storage::url($targetPath),
                    'category_id' => $category->id,
                ]);

                $totalCreated++;
            }
        }

        $this->command->info("Service Providers seeded successfully! Total created: {$totalCreated}");
    }

    /**
     * Generate a business name related to the category
     */
    private function generateBusinessName(string $category): string
    {
        $prefixes = [
            'Global', 'Premium', 'Elite', 'Advanced', 'Unified', 'Strategic', 'Prime',
            'Peak', 'Superior', 'Optimal', 'Dynamic', 'Innovative', 'Progressive'
        ];

        $suffixes = [
            'Solutions', 'Services', 'Group', 'Partners', 'Associates', 'Experts',
            'Professionals', 'Consultants', 'Network', 'Alliance', 'Ventures'
        ];

        $categoryWords = [
            'Technology' => ['Tech', 'Digital', 'Cyber', 'Data', 'Cloud', 'Innovative'],
            'Healthcare' => ['Health', 'Medical', 'Care', 'Wellness', 'Therapeutic'],
            'Finance' => ['Financial', 'Capital', 'Wealth', 'Asset', 'Equity'],
            'Education' => ['Learning', 'Academic', 'Knowledge', 'Educational', 'Smart'],
            'Hospitality' => ['Hospitality', 'Comfort', 'Leisure', 'Experience'],
            'Logistics' => ['Logistics', 'Transport', 'Shipping', 'Delivery', 'Supply'],
            'Manufacturing' => ['Manufacturing', 'Production', 'Industrial', 'Factory'],
            'Real Estate' => ['Realty', 'Properties', 'Estate', 'Housing', 'Construction'],
            'Retail' => ['Retail', 'Store', 'Commerce', 'Market', 'Shopping'],
            'Marketing' => ['Marketing', 'Brand', 'Media', 'Promotion', 'Creative']
        ];

        $prefix = $prefixes[array_rand($prefixes)];
        $suffix = $suffixes[array_rand($suffixes)];

        $categoryWord = $categoryWords[$category] ?? [$category];
        $middleWord = $categoryWord[array_rand($categoryWord)];

        // Randomly decide if we should use the middle word
        if (rand(0, 1) === 1) {
            return "{$prefix} {$middleWord} {$suffix}";
        }

        return "{$prefix} {$suffix}";
    }

    /**
     * Generate a short description related to the category
     */
    private function generateShortDescription(string $category): string
    {
        $descriptions = [
            'Technology' => [
                'Innovative IT solutions for businesses of all sizes',
                'Custom software development and digital transformation',
                'Cloud infrastructure and cybersecurity services',
                'Enterprise systems integration and support',
                'Mobile app development and IoT solutions'
            ],
            'Healthcare' => [
                'Comprehensive healthcare services with a patient-centered approach',
                'Medical consulting and specialized treatment options',
                'Preventive care and wellness program development',
                'Healthcare management and administration solutions',
                'Telemedicine and remote health monitoring services'
            ],
            'Finance' => [
                'Strategic financial planning and wealth management',
                'Investment advisory and portfolio management services',
                'Tax planning and accounting solutions for businesses',
                'Personal finance consulting and retirement planning',
                'Digital payment processing and financial technology'
            ],
            'Education' => [
                'Personalized learning programs for students of all ages',
                'Professional development and corporate training services',
                'Curriculum development and educational consulting',
                'Online learning platforms and educational technology',
                'Academic counseling and student success programs'
            ],
            'Hospitality' => [
                'Premium accommodation and event hosting services',
                'Restaurant management and catering solutions',
                'Customer experience design for hospitality businesses',
                'Tourism development and destination management',
                'Hospitality staff training and operations consulting'
            ],
            'Logistics' => [
                'End-to-end supply chain management solutions',
                'Global shipping and freight forwarding services',
                'Warehouse management and inventory optimization',
                'Transportation network design and route optimization',
                'Last-mile delivery and logistics technology'
            ],
            'Manufacturing' => [
                'Custom manufacturing solutions for diverse industries',
                'Production line optimization and automation',
                'Quality control systems and compliance management',
                'Manufacturing process design and implementation',
                'Sustainable manufacturing and waste reduction'
            ],
            'Real Estate' => [
                'Premium property development and management services',
                'Commercial and residential real estate solutions',
                'Property investment and portfolio management',
                'Real estate consulting and market analysis',
                'Facility management and maintenance services'
            ],
            'Retail' => [
                'Omnichannel retail solutions and customer experience',
                'Retail analytics and inventory management',
                'Store design and retail space optimization',
                'E-commerce development and digital retail platforms',
                'Customer retention and loyalty program management'
            ],
            'Marketing' => [
                'Integrated marketing campaigns and brand development',
                'Digital marketing strategy and implementation',
                'Content creation and social media management',
                'Marketing analytics and performance measurement',
                'Public relations and corporate communications'
            ]
        ];

        $categoryDescriptions = $descriptions[$category] ?? ["Professional {$category} services for businesses and individuals"];

        return $categoryDescriptions[array_rand($categoryDescriptions)];
    }

    /**
     * Generate a longer description related to the category
     */
    private function generateDescription(string $category): string
    {
        $intro = $this->generateShortDescription($category);

        $details = [
            'Technology' => [
                'Our team of certified engineers and developers works closely with clients to identify technological needs and implement tailored solutions. We specialize in cloud migration, custom software development, cybersecurity, and IT infrastructure management.',
                'With over a decade of experience in the technology sector, we have successfully completed projects for clients ranging from startups to Fortune 500 companies. Our agile methodology ensures timely delivery and continuous improvement.',
                'We leverage cutting-edge technologies including artificial intelligence, blockchain, and IoT to solve complex business challenges. Our solutions are scalable, secure, and designed for optimal performance.'
            ],
            'Healthcare' => [
                'Our healthcare professionals are committed to providing evidence-based care with compassion and respect. We offer comprehensive medical services, preventive care programs, and specialized treatments tailored to individual patient needs.',
                'With state-of-the-art facilities and cutting-edge medical technology, we deliver high-quality healthcare services that improve patient outcomes and enhance quality of life. Our multidisciplinary approach ensures comprehensive care coordination.',
                'We believe in patient-centered care that addresses both physical and emotional wellbeing. Our integrated health services include diagnostics, treatment, rehabilitation, and ongoing wellness support.'
            ],
            'Finance' => [
                'Our team of certified financial advisors provides personalized financial planning services based on thorough analysis of client goals, risk tolerance, and market conditions. We specialize in retirement planning, investment management, and tax optimization strategies.',
                'With a fiduciary approach to wealth management, we always prioritize our clients\' best interests. Our transparent fee structure and regular performance reporting ensure accountability and trust in our financial relationship.',
                'We combine traditional financial wisdom with innovative approaches to create robust financial strategies. Our services include portfolio management, estate planning, insurance solutions, and business succession planning.'
            ],
            'Education' => [
                'Our educational methodologies are based on proven learning science and pedagogical research. We develop customized learning experiences that address diverse learning styles, abilities, and educational objectives.',
                'With experienced educators and instructional designers, we create engaging and effective educational content that promotes deep understanding and skill mastery. Our approach balances theoretical knowledge with practical application.',
                'We leverage educational technology to enhance access, engagement, and learning outcomes. Our programs include interactive elements, formative assessments, and personalized learning paths based on student progress and performance.'
            ],
            'Hospitality' => [
                'Our hospitality services are designed to create memorable guest experiences through attention to detail, personalized service, and exceptional amenities. We prioritize customer satisfaction at every touchpoint of the guest journey.',
                'With extensive experience in the hospitality industry, we understand the importance of consistent service quality, operational efficiency, and staff training. Our management approaches optimize both guest satisfaction and business profitability.',
                'We combine traditional hospitality values with innovative approaches to accommodate evolving customer expectations. Our services include accommodation management, event planning, food and beverage operations, and hospitality consulting.'
            ],
            'Logistics' => [
                'Our logistics solutions optimize the movement of goods through the entire supply chain, from sourcing and production to warehousing and final delivery. We leverage advanced technology for real-time tracking, route optimization, and inventory management.',
                'With a global network of transportation partners and distribution centers, we can efficiently handle shipments of any size to any destination. Our integrated approach ensures seamless coordination across all logistics functions.',
                'We focus on cost-effective logistics operations that maintain service quality and reliability. Our solutions include freight forwarding, warehouse management, customs brokerage, and supply chain consulting.'
            ],
            'Manufacturing' => [
                'Our manufacturing capabilities combine precision engineering, quality materials, and efficient production processes to deliver superior products. We adhere to rigorous quality control standards and continuous improvement methodologies.',
                'With advanced production facilities and skilled technical staff, we can handle manufacturing projects of varying complexity and scale. Our approach balances automation and craftsmanship to achieve optimal results.',
                'We implement lean manufacturing principles to minimize waste, reduce costs, and improve production efficiency. Our services include product design support, prototype development, full-scale production, and supply chain integration.'
            ],
            'Real Estate' => [
                'Our real estate services cover the entire property lifecycle, from acquisition and development to management and disposition. We conduct thorough market analyses to identify opportunities and optimize property values.',
                'With deep knowledge of local and regional real estate markets, we provide informed guidance for property investments and development projects. Our approach balances short-term returns with long-term value appreciation.',
                'We manage properties with a focus on tenant satisfaction, operational efficiency, and asset preservation. Our services include property management, facility maintenance, leasing, and real estate portfolio optimization.'
            ],
            'Retail' => [
                'Our retail solutions help businesses create seamless shopping experiences across physical and digital channels. We focus on customer journey mapping, touchpoint optimization, and personalized engagement strategies.',
                'With expertise in retail analytics, we help businesses understand customer behavior, optimize inventory, and improve merchandising strategies. Our data-driven approach informs decision-making at all levels of retail operations.',
                'We support retailers in adapting to changing market conditions and consumer preferences. Our services include retail concept development, store design, e-commerce integration, and customer loyalty programs.'
            ],
            'Marketing' => [
                'Our marketing strategies are designed to build brand awareness, engage target audiences, and drive measurable business results. We develop integrated campaigns that leverage appropriate channels for maximum impact.',
                'With a creative approach grounded in marketing analytics, we create compelling content and campaigns that resonate with audiences. Our process includes thorough market research, audience segmentation, and performance tracking.',
                'We help businesses establish clear brand identities and consistent messaging across all customer touchpoints. Our services include brand development, content marketing, digital advertising, and marketing performance analysis.'
            ]
        ];

        $categoryDetails = $details[$category] ?? ["We provide comprehensive {$category} services tailored to meet the unique needs of our clients. Our experienced team works closely with each client to understand their specific requirements and deliver customized solutions that add value and drive results."];

        $conclusion = "Contact us today to learn how our " . strtolower($category) . " solutions can help your business succeed.";

        // Combine intro, 1-2 detail paragraphs, and conclusion
        $fullDescription = $intro . "\n\n";
        $fullDescription .= $categoryDetails[array_rand($categoryDetails)] . "\n\n";

        // 50% chance to add a second paragraph
        if (rand(0, 1) === 1) {
            $remainingDetails = array_diff($categoryDetails, [$fullDescription]);
            if (!empty($remainingDetails)) {
                $fullDescription .= $remainingDetails[array_rand($remainingDetails)] . "\n\n";
            }
        }

        $fullDescription .= $conclusion;

        return $fullDescription;
    }
}
