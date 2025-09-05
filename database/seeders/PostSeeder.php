<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $adminUser = User::where('email', 'admin@modermblog.com')->first();
        
        if (!$adminUser) {
            $this->command->error('Admin user not found. Please run UserSeeder first.');
            return;
        }

        $categories = Category::all();
        
        if ($categories->isEmpty()) {
            $this->command->error('No categories found. Please run CategorySeeder first.');
            return;
        }

        $posts = [
            [
                'title' => 'Getting Started with Laravel 10',
                'slug' => 'getting-started-with-laravel-10',
                'excerpt' => 'Learn the basics of Laravel 10 and discover what makes it one of the most popular PHP frameworks.',
                'content' => 'Laravel 10 continues to be one of the most popular PHP frameworks, offering elegant syntax and powerful features. In this comprehensive guide, we\'ll explore the key features that make Laravel 10 special.

## What\'s New in Laravel 10

Laravel 10 introduces several exciting features:

- **Improved Performance**: Better optimization for faster response times
- **Enhanced Security**: New security features to protect your applications
- **Developer Experience**: Improved debugging and development tools

## Installation

Getting started with Laravel 10 is simple:

```bash
composer create-project laravel/laravel my-project
```

## Key Features

Laravel offers many powerful features out of the box:

1. **Eloquent ORM**: Beautiful, simple ActiveRecord implementation
2. **Blade Templating**: Powerful templating engine
3. **Artisan Console**: Command-line interface for Laravel
4. **Migration System**: Version control for your database

## Conclusion

Laravel 10 continues to set the standard for modern PHP development. Its elegant syntax and comprehensive feature set make it an excellent choice for both beginners and experienced developers.',
                'status' => 'published',
                'published_at' => now()->subDays(5),
                'category' => 'technology',
            ],
            [
                'title' => '10 Healthy Morning Habits That Will Transform Your Life',
                'slug' => '10-healthy-morning-habits-transform-life',
                'excerpt' => 'Discover powerful morning habits that successful people use to start their day right and boost productivity.',
                'content' => 'Your morning routine sets the tone for your entire day. Research shows that people with consistent morning habits are more productive, happier, and successful. Here are 10 proven morning habits that can transform your life.

## 1. Wake Up Early

Starting your day early gives you quiet time to focus on yourself before the world wakes up. Try waking up just 30 minutes earlier than usual.

## 2. Hydrate Immediately

Your body has been without water for 6-8 hours. Start with a large glass of water to kickstart your metabolism.

## 3. Practice Mindfulness

Spend 5-10 minutes in meditation or deep breathing. This helps reduce stress and increase focus for the day ahead.

## 4. Exercise

Even 15 minutes of movement can boost your energy levels and improve your mood throughout the day.

## 5. Eat a Nutritious Breakfast

Fuel your body with protein, healthy fats, and complex carbohydrates to maintain steady energy levels.

## 6. Review Your Goals

Take a few minutes to review your daily and long-term goals. This keeps you focused on what matters most.

## 7. Read Something Inspiring

Start your day with positive input by reading something educational or motivational.

## 8. Plan Your Day

Spend 5 minutes planning your priorities for the day. This helps you stay organized and focused.

## 9. Practice Gratitude

Write down three things you\'re grateful for. This simple practice can significantly improve your mood and outlook.

## 10. Avoid Phone/Social Media

Keep your phone away for the first hour after waking up. This prevents information overload and helps you start the day intentionally.',
                'status' => 'published',
                'published_at' => now()->subDays(3),
                'category' => 'lifestyle',
            ],
            [
                'title' => 'Hidden Gems of Southeast Asia: 7 Destinations You Must Visit',
                'slug' => 'hidden-gems-southeast-asia-destinations',
                'excerpt' => 'Explore breathtaking destinations in Southeast Asia that are off the beaten path but absolutely worth visiting.',
                'content' => 'Southeast Asia is filled with incredible destinations that go beyond the typical tourist spots. Here are seven hidden gems that offer authentic experiences and breathtaking beauty.

## 1. Siargao Island, Philippines

Known as the surfing capital of the Philippines, Siargao offers pristine beaches, crystal-clear lagoons, and the famous Cloud 9 surf break.

### What to Do:
- Surf the legendary Cloud 9 waves
- Island hop to nearby islets
- Visit the enchanting Sugba Lagoon
- Explore Sohoton Cove

## 2. Koh Rong Samloem, Cambodia

This peaceful island offers some of the most beautiful beaches in Southeast Asia without the crowds.

### Highlights:
- Saracen Bay beach with powdery white sand
- Bioluminescent plankton at night
- Excellent snorkeling and diving
- Affordable accommodation options

## 3. Hpa-An, Myanmar

A charming town surrounded by dramatic limestone karsts and ancient caves filled with Buddha statues.

### Must-See:
- Sadan Cave boat ride
- Mount Zwegabin sunrise hike
- Kyauk Kalap Pagoda on a rock pillar
- Lumbini Garden with thousands of Buddha statues

## 4. Flores Island, Indonesia

Beyond the famous Komodo dragons, Flores offers stunning landscapes and rich culture.

### Experiences:
- Witness Komodo dragons in their natural habitat
- Snorkel with manta rays
- Explore traditional villages
- Visit the three-colored crater lakes of Kelimutu

## 5. Cat Ba Island, Vietnam

The largest island in Ha Long Bay, offering a perfect blend of adventure and relaxation.

### Activities:
- Trek through Cat Ba National Park
- Kayak through hidden lagoons
- Rock climb on limestone cliffs
- Relax on secluded beaches

## 6. Kampot Province, Cambodia

Known for its famous pepper plantations and laid-back riverside town atmosphere.

### Don\'t Miss:
- Tour pepper plantations
- Explore Bokor National Park
- Take a sunset river cruise
- Visit salt fields and traditional fishing villages

## 7. Gili Meno, Indonesia

The quiet middle child of the Gili Islands, perfect for couples and those seeking tranquility.

### Perfect For:
- Romantic getaways
- Snorkeling with sea turtles
- Watching incredible sunsets
- Experiencing local island life

## Planning Your Trip

The best time to visit most of these destinations is during the dry season (November to April). Consider island-hopping to experience multiple destinations in one trip.

Each of these hidden gems offers something unique, from adventure activities to cultural experiences and pure relaxation. Pack your sense of adventure and get ready to discover the authentic beauty of Southeast Asia!',
                'status' => 'published',
                'published_at' => now()->subDays(7),
                'category' => 'travel',
            ],
            [
                'title' => 'The Ultimate Guide to Mediterranean Pasta',
                'slug' => 'ultimate-guide-mediterranean-pasta',
                'excerpt' => 'Master the art of Mediterranean pasta with fresh ingredients, authentic techniques, and delicious recipes.',
                'content' => 'Mediterranean pasta dishes are known for their simplicity, fresh ingredients, and incredible flavors. This guide will help you master the techniques and create restaurant-quality pasta dishes at home.

## The Foundation: Quality Ingredients

### Pasta
- Use high-quality durum wheat pasta
- Fresh pasta works best for delicate sauces
- Dried pasta is perfect for robust sauces

### Olive Oil
- Extra virgin olive oil is essential
- Look for cold-pressed varieties
- Store in a cool, dark place

### Tomatoes
- San Marzano tomatoes for sauce
- Fresh cherry tomatoes for simple dishes
- Always use ripe, in-season tomatoes when possible

## Essential Techniques

### Cooking Pasta Perfectly
1. Use plenty of salted water (1 tablespoon salt per liter)
2. Cook until al dente (firm to the bite)
3. Reserve pasta water before draining
4. Never rinse cooked pasta

### Building Flavor
- Start with a soffritto (onion, carrot, celery)
- Use garlic generously but don\'t burn it
- Fresh herbs added at the end
- Finish with good olive oil

## Classic Recipes

### Spaghetti Aglio e Olio
**Ingredients:**
- 400g spaghetti
- 6 cloves garlic, thinly sliced
- 1/2 cup extra virgin olive oil
- Red pepper flakes
- Fresh parsley
- Pecorino Romano cheese

**Method:**
1. Cook spaghetti until al dente
2. Heat olive oil and sauté garlic until golden
3. Add red pepper flakes
4. Toss with pasta and pasta water
5. Finish with parsley and cheese

### Penne Arrabbiata
**Ingredients:**
- 400g penne pasta
- 400g crushed tomatoes
- 4 cloves garlic
- 2 dried chilies
- Fresh basil
- Extra virgin olive oil

**Method:**
1. Sauté garlic and chilies in olive oil
2. Add crushed tomatoes and simmer
3. Cook penne until al dente
4. Combine with sauce
5. Finish with fresh basil

### Linguine alle Vongole
**Ingredients:**
- 400g linguine
- 1kg fresh clams
- White wine
- Garlic
- Parsley
- Extra virgin olive oil

**Method:**
1. Clean clams thoroughly
2. Sauté garlic in olive oil
3. Add clams and white wine
4. Cook until clams open
5. Toss with cooked linguine and parsley

## Pro Tips

1. **Salt Your Pasta Water**: It should taste like seawater
2. **Save Pasta Water**: The starchy water helps bind sauces
3. **Don\'t Overdress**: Let the pasta be the star
4. **Timing is Everything**: Have all components ready simultaneously
5. **Quality Over Quantity**: Use fewer, better ingredients

## Wine Pairings

- Light tomato sauces: Chianti Classico
- Seafood pasta: Pinot Grigio or Vermentino
- Cream-based sauces: Chardonnay
- Spicy dishes: Primitivo or Sangiovese

## Conclusion

Mediterranean pasta is about celebrating simple, quality ingredients. Master these basics, and you\'ll be creating incredible pasta dishes that transport you straight to the Italian coast!',
                'status' => 'published',
                'published_at' => now()->subDays(2),
                'category' => 'food-cooking',
            ],
            [
                'title' => 'Building a Sustainable Home Gym on a Budget',
                'slug' => 'building-sustainable-home-gym-budget',
                'excerpt' => 'Create an effective home gym without breaking the bank. Essential equipment and space-saving solutions.',
                'content' => 'Creating a home gym doesn\'t have to cost thousands of dollars or require a dedicated room. With smart planning and the right equipment choices, you can build an effective workout space that fits your budget and lifestyle.

## Planning Your Space

### Assess Your Available Area
- Measure your available space carefully
- Consider ceiling height for overhead movements
- Ensure proper ventilation
- Plan for equipment storage

### Multi-Purpose Spaces
- Living room corners work great
- Garage or basement options
- Outdoor spaces for cardio
- Bedroom floor space for yoga/stretching

## Essential Equipment (Budget: $200-400)

### 1. Resistance Bands Set ($20-30)
- Provides full-body resistance training
- Takes up minimal storage space
- Various resistance levels available
- Can replicate many gym machines

### 2. Adjustable Dumbbells ($80-150)
- Space-efficient strength training
- Multiple weight options in one set
- Great for both beginners and advanced users
- Essential for upper body development

### 3. Exercise Mat ($15-25)
- Crucial for floor exercises
- Yoga and stretching sessions
- Core workouts and planks
- Provides cushioning and grip

### 4. Pull-up Bar ($20-40)
- Doorway or wall-mounted options
- Essential for back and arm development
- Can be used for hanging exercises
- Takes up zero floor space

### 5. Kettlebell ($25-50)
- Single weight provides full-body workouts
- Excellent for cardio and strength
- Space-efficient storage
- Versatile exercise options

## Cardio Solutions

### Low-Cost Options
- Jump rope ($10-15)
- YouTube workout videos (Free)
- Running/walking outdoors (Free)
- Stair climbing (Free)

### Mid-Range Options
- Stationary bike ($100-200 used)
- Rowing machine ($150-300 used)
- Elliptical machine ($200-400 used)

## Creating Workout Programs

### Full-Body Circuit (3x per week)
1. Warm-up: 5 minutes jumping jacks/arm circles
2. Squats: 3 sets of 12-15
3. Push-ups: 3 sets of 8-12
4. Pull-ups: 3 sets of 5-8
5. Plank: 3 sets of 30-60 seconds
6. Resistance band rows: 3 sets of 12-15
7. Cool-down: 5 minutes stretching

### Upper/Lower Split (4x per week)
**Upper Day:**
- Pull-ups/assisted pull-ups
- Dumbbell rows
- Push-ups/dumbbell press
- Resistance band exercises
- Core work

**Lower Day:**
- Squats and squat variations
- Lunges
- Deadlifts with dumbbells/kettlebell
- Calf raises
- Glute bridges

## Money-Saving Tips

### Buy Used Equipment
- Check Craigslist, Facebook Marketplace
- Garage sales and estate sales
- Gym equipment stores often have used sections
- End-of-year fitness equipment sales

### DIY Solutions
- Water jugs as weights
- Towels for sliders
- Stairs for cardio
- Books in backpack for added weight

### Gradual Building
- Start with basics, add equipment over time
- Focus on versatile, multi-use items
- Wait for sales and discounts
- Consider seasonal purchasing

## Maximizing Your Investment

### Proper Maintenance
- Clean equipment regularly
- Store properly to prevent damage
- Follow manufacturer guidelines
- Address issues promptly

### Stay Motivated
- Set up in a pleasant, well-lit area
- Create workout playlists
- Track your progress
- Find online workout partners

## Advanced Additions (As Budget Allows)

- TRX suspension trainer ($100-150)
- Foam roller ($20-40)
- Additional dumbbells or plates
- Bench for more exercise variety

## Conclusion

A effective home gym is about consistency, not equipment cost. Focus on the basics, use what you have creatively, and gradually build your setup. The best gym is the one you\'ll actually use!

Remember: The most expensive gym membership is the one you don\'t use. Start simple, stay consistent, and enjoy the convenience of working out at home.',
                'status' => 'published',
                'published_at' => now()->subDays(1),
                'category' => 'health-fitness',
            ],
            [
                'title' => 'The Future of Remote Work: Trends and Predictions for 2024',
                'slug' => 'future-remote-work-trends-predictions-2024',
                'excerpt' => 'Explore the evolving landscape of remote work and what businesses and employees can expect in the coming year.',
                'content' => 'The remote work revolution that began during the pandemic has permanently changed how we think about work. As we move through 2024, new trends are emerging that will shape the future of distributed teams and flexible work arrangements.

## Current State of Remote Work

### The Numbers
- 35% of the workforce can work remotely full-time
- 42% work in hybrid arrangements
- 87% of workers offered remote work take advantage of it
- Companies report 25% increase in productivity

### Geographic Distribution
Remote work has enabled talent distribution across:
- Rural areas with lower cost of living
- International markets for 24/7 coverage
- Access to specialized skills regardless of location
- Reduced urban congestion

## Emerging Trends for 2024

### 1. Hybrid-First Policies
Companies are moving beyond "remote-friendly" to "hybrid-first":
- Default to flexible arrangements
- Office space redesigned for collaboration, not individual work
- Technology investments in seamless hybrid experiences
- New management training for distributed teams

### 2. Results-Only Work Environment (ROWE)
Focus shifting from time tracking to outcome measurement:
- Goal-based performance metrics
- Flexible working hours across time zones
- Emphasis on deliverables over presence
- Trust-based management approaches

### 3. Digital Nomad Integration
Companies embracing location-independent workforce:
- Visa partnerships with nomad-friendly countries
- Stipends for co-working spaces and travel
- Tax and legal frameworks for international remote work
- Cultural adaptation and communication training

### 4. AI-Powered Remote Collaboration
Technology enhancing distributed teamwork:
- AI meeting summaries and action items
- Automated scheduling across time zones
- Real-time language translation
- Virtual presence and avatar technologies

### 5. Mental Health and Wellness Focus
Addressing remote work challenges:
- Digital wellness programs
- Mandatory disconnect policies
- Virtual team building activities
- Mental health stipends and support

## Challenges and Solutions

### Communication Barriers
**Challenge**: Reduced informal interactions and context
**Solutions**:
- Structured communication protocols
- Regular virtual coffee chats
- Asynchronous communication tools
- Clear documentation practices

### Company Culture Maintenance
**Challenge**: Maintaining shared values and identity
**Solutions**:
- Virtual onboarding programs
- Digital culture initiatives
- Remote team traditions
- Value-based hiring practices

### Career Development
**Challenge**: Reduced mentorship and growth opportunities
**Solutions**:
- Formal mentorship programs
- Virtual shadowing opportunities
- Online learning and development budgets
- Clear remote advancement pathways

## Technology Infrastructure Requirements

### Essential Tools
- High-quality video conferencing systems
- Cloud-based project management platforms
- Secure VPN and cybersecurity measures
- Digital whiteboarding and collaboration tools

### Emerging Technologies
- VR/AR for immersive meetings
- Advanced analytics for productivity insights
- IoT integration for home office optimization
- Blockchain for secure remote work verification

## Impact on Real Estate and Cities

### Commercial Real Estate
- Reduced demand for traditional office space
- Increase in flexible co-working arrangements
- Repurposing of office buildings
- Focus on collaboration spaces over individual offices

### Urban Planning
- Distributed population reducing urban density
- Investment in rural infrastructure
- Changes in transportation patterns
- New economic opportunities in smaller cities

## Predictions for 2025 and Beyond

### Workplace Evolution
- 50% of companies will be fully distributed by 2027
- Physical offices will become "experience centers"
- Four-day work weeks will become standard
- AI assistants will handle routine remote work tasks

### Skill Development
Most in-demand remote work skills:
- Digital communication and presentation
- Cross-cultural collaboration
- Self-management and discipline
- Technology proficiency across platforms
- Emotional intelligence in virtual settings

### Economic Impact
- $1.3 trillion in combined savings for employers and employees
- 25% reduction in commercial real estate demand
- Growth of rural and secondary city economies
- New service industries supporting remote workers

## Best Practices for Organizations

### Leadership Strategies
1. Invest in manager training for remote team leadership
2. Develop clear remote work policies and expectations
3. Prioritize results and outcomes over activity
4. Foster inclusive practices for hybrid teams
5. Regularly survey and adapt to employee needs

### Technology Investments
- Robust cybersecurity infrastructure
- User-friendly collaboration platforms
- Employee device and internet stipends
- Regular technology training and support

### Culture Building
- Virtual all-hands meetings and celebrations
- Cross-functional project collaboration
- Remote mentorship and buddy systems
- Digital recognition and reward programs

## Preparing for the Future

### For Employees
- Develop strong digital communication skills
- Create dedicated home workspace
- Establish clear work-life boundaries
- Build professional networks virtually
- Stay updated on remote work technologies

### For Employers
- Reassess real estate needs and investments
- Update HR policies for distributed workforce
- Invest in management training and development
- Plan for global talent acquisition
- Develop robust remote onboarding processes

## Conclusion

The future of remote work is not just about working from home—it\'s about creating flexible, results-oriented work environments that attract top talent regardless of location. Organizations that embrace this shift and invest in the right tools, training, and culture will have a significant competitive advantage.

The key to success lies in intentional design of remote work experiences, continuous adaptation to new technologies, and maintaining human connection in a digital world. As we move forward, the companies that thrive will be those that view remote work not as a necessity, but as a strategic advantage.',
                'status' => 'draft',
                'published_at' => null,
                'category' => 'business',
            ],
            [
                'title' => 'The Renaissance of Analog Art in a Digital World',
                'slug' => 'renaissance-analog-art-digital-world',
                'excerpt' => 'Exploring why traditional art forms are experiencing a revival in our increasingly digital society.',
                'content' => 'In an age dominated by digital screens and virtual experiences, something unexpected is happening in the art world: a renaissance of analog art forms. From vinyl records to film photography, hand lettering to oil painting, traditional media are experiencing a remarkable revival.

## The Digital Fatigue Phenomenon

### Screen Exhaustion
After spending 8+ hours daily staring at screens, people crave tactile, physical experiences:
- Texture and dimensionality that screens cannot provide
- The meditative quality of hands-on creation
- Imperfections that make art uniquely human
- The permanence of physical objects

### Authenticity in an AI Age
As artificial intelligence creates increasingly sophisticated digital art:
- Human craftsmanship becomes more valued
- The "hand of the artist" gains significance
- Process becomes as important as the final product
- Unique, unreproducible works gain premium value

## Resurgent Art Forms

### Film Photography
Despite digital cameras\' convenience, film photography is booming:

**Why Film Appeals:**
- Intentional, slower creative process
- Unique aesthetic qualities
- Limited shots encourage thoughtful composition
- Chemical development adds mystery and surprise
- Physical prints create lasting artifacts

**Popular Formats:**
- 35mm for beginners and professionals
- Medium format for studio work
- Instant film for immediate gratification
- Large format for ultimate image quality

### Vinyl Records and Analog Music
Music consumption has returned to physical formats:

**The Vinyl Revival:**
- Tactile album artwork and liner notes
- Ritual of playing records
- Warmer, fuller sound quality
- Supporting artists through physical purchases
- Social aspect of record collecting

**Impact on Artists:**
- Album sequencing regains importance
- Cover art becomes crucial marketing tool
- Limited editions create collector value
- Live performance emphasis increases

### Traditional Painting and Drawing
Despite digital art tools, traditional media flourish:

**Oil Painting Renaissance:**
- Slow, contemplative process
- Rich texture and color depth
- Historical techniques being rediscovered
- Plein air painting communities growing
- Gallery preference for physical works

**Drawing and Sketching:**
- Bullet journaling and analog planning
- Urban sketching movement
- Life drawing classes popularity
- Pencil and charcoal technique workshops
- Sketchbook culture on social media

### Hand Lettering and Calligraphy
Typography enthusiasts return to pen and ink:

**Modern Calligraphy:**
- Weddings and event applications
- Social media driving interest
- Therapeutic and meditative qualities
- Personalization in digital age
- Small business opportunities

### Printmaking Revival
Traditional printing techniques gain new audiences:

**Screen Printing:**
- Band merchandise and art prints
- DIY culture and maker spaces
- Limited edition appeal
- Workshop and class popularity

**Letterpress:**
- Wedding invitations and stationery
- Art book and zine production
- Tactile quality valued over efficiency
- Historical machinery restoration

## The Maker Movement Connection

### DIY Culture
The maker movement emphasizes hands-on creation:
- Workshops and classes in traditional techniques
- Community studios and shared spaces
- Intergenerational skill transfer
- Social media sharing analog processes

### Slow Art Movement
Emphasis on process over product:
- Mindfulness and meditation through art
- Quality time investment
- Sustainable practices
- Connection to materials and tools

## Digital-Analog Hybrid Approaches

### Documentation and Sharing
Artists combine both worlds:
- Photographing analog work for social media
- Time-lapse videos of creation process
- Online tutorials for traditional techniques
- Digital portfolios of physical work

### Modern Tools for Traditional Arts
Technology enhances rather than replaces:
- LED light boxes for drawing
- Digital color matching for mixing paints
- Online communities for technique sharing
- E-commerce platforms for selling analog art

## Economic Impact

### Market Growth
Traditional art supplies see increased demand:
- Art supply stores report double-digit growth
- Premium material markets expanding
- Workshop and class enrollment rising
- Vintage tool collection markets

### New Business Models
Artists find success combining digital marketing with analog creation:
- Instagram as primary marketing platform
- Etsy and handmade marketplaces
- Subscription boxes for art supplies
- Online tutorial and course sales

## Educational Renaissance

### Art School Curriculum
Educational institutions adapt to renewed interest:
- Traditional technique course expansion
- Master craftsperson visiting programs
- Historical art method research
- Cross-cultural traditional art studies

### Workshop Culture
Community-based learning thrives:
- Weekend intensive workshops
- Mentorship programs
- Skill-sharing cooperatives
- Cultural center programming

## Psychological and Social Benefits

### Mental Health Impact
Analog art creation provides unique benefits:
- Stress reduction through focused attention
- Tangible sense of accomplishment
- Break from digital overwhelm
- Mindfulness and present-moment awareness

### Community Building
Traditional arts foster human connection:
- Group classes and workshops
- Art-making social events
- Skill sharing between generations
- Local art scene development

### Cultural Preservation
Revival helps maintain cultural heritage:
- Traditional techniques documentation
- Master craftsperson knowledge transfer
- Cultural identity through art forms
- Historical technique research and revival

## Challenges and Considerations

### Accessibility Issues
Traditional arts face barriers:
- Higher cost of quality materials
- Need for dedicated workspace
- Time investment requirements
- Learning curve steeper than digital tools

### Environmental Considerations
Material choices impact sustainability:
- Toxic solvents and chemicals
- Sustainable material sourcing
- Waste management practices
- Energy consumption of kilns and equipment

## Future Predictions

### Integration Trends
The future likely holds:
- Seamless digital-analog workflows
- Smart tools that enhance traditional techniques
- Augmented reality guides for traditional arts
- Blockchain authentication for physical artworks

### Cultural Shifts
Long-term changes may include:
- Increased value placed on handmade objects
- Slower, more intentional creative practices
- Hybrid art forms combining digital and analog
- Educational emphasis on both skill sets

## Conclusion

The renaissance of analog art in our digital world represents more than nostalgia—it\'s a fundamental human need for tactile, authentic creative expression. As we become increasingly digitally saturated, the irreplaceable qualities of traditional art forms—their physicality, imperfection, and human touch—become more precious.

This revival doesn\'t represent a rejection of digital tools, but rather a recognition that both approaches offer unique values. The future of art lies not in choosing between digital and analog, but in thoughtfully combining both to create richer, more meaningful creative experiences.

Whether you\'re a seasoned digital artist curious about traditional media or a complete beginner drawn to the tactile nature of analog art, there has never been a better time to explore these timeless creative practices. In our rush toward the digital future, we\'re rediscovering the irreplaceable value of the human hand in creating art.',
                'status' => 'draft',
                'published_at' => null,
                'category' => 'arts-culture',
            ],
        ];

        foreach ($posts as $postData) {
            // Find category by slug
            $category = $categories->where('slug', $postData['category'])->first();
            
            if (!$category) {
                $this->command->warn("Category '{$postData['category']}' not found for post '{$postData['title']}'");
                continue;
            }

            // Create post
            Post::firstOrCreate(
                ['slug' => $postData['slug']],
                [
                    'title' => $postData['title'],
                    'slug' => $postData['slug'],
                    'excerpt' => $postData['excerpt'],
                    'content' => $postData['content'],
                    'status' => $postData['status'],
                    'published_at' => $postData['published_at'],
                    'category_id' => $category->id,
                    'user_id' => $adminUser->id,
                ]
            );
        }

        $this->command->info('Posts seeded successfully!');
    }
}