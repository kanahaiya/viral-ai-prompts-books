from docx import Document
from docx.shared import Inches

sections = [
    ('Hero', [
        'Create Stunning AI Images in Minutes, Even If You’ve Never Used AI Before',
        'Turn selfies, family photos, ideas, and simple concepts into scroll-stopping AI visuals using ready-made fill-in-the-blank templates.',
        'No design skills.',
        'No prompt writing.',
        'No technical knowledge needed.',
        'No prompt-writing required',
        'Works with ChatGPT, Midjourney, Firefly',
        'First result in your first session',
        'One-time payment · Instant access · No subscription',
        'Trusted by 200+ paying customers · 4.6★ average rating',
    ]),
    ('Pain Agitation', [
        'Everywhere You Look, People Are Posting Incredible AI Art…',
        'Anime portraits. Cinematic posters. Action figure toy boxes. Dreamy edits. Viral Instagram visuals.',
        'And honestly? You want to create those too.',
        'But the moment AI tools open, the frustration starts.',
        'Random prompts giving random results',
        'No idea what to type',
        'Hours wasted tweaking words',
        'Saved AI art ideas never getting created',
        'AI tools feeling technical and overwhelming',
        'Final images looking average instead of impressive',
        'Instead of feeling creative, the whole process starts feeling confusing.',
        'Stop Struggling With Prompts',
    ]),
    ('Solution Bridge', [
        'This Makes AI Image Creation Feel Easy',
        'No prompt engineering.',
        'No complicated tutorials.',
        'No blank-screen frustration.',
        'Pick a style',
        'Fill in a few blanks',
        'Paste the prompt',
        'Generate stunning AI visuals in minutes',
        'Create: Anime portraits, Cinematic posters, Action figure toy boxes, Pet transformations, Professional headshots, Viral-style AI art.',
        'Everything is designed for complete beginners, even if this is the very first time using AI tools.',
    ]),
    ('Benefits', [
        'What You’ll Be Able To Create',
        'Create AI images that genuinely make friends and followers stop and react.',
        'Turn ordinary photos and ideas into visuals that look creative, cinematic, and impressive.',
        'Make viral-style AI art without spending weeks learning complicated prompting.',
        'Finally create the kind of AI images usually seen only on trending Instagram pages.',
        'Go from “I have no idea what to type” to creating stunning visuals in minutes.',
        'Make emotional gifts, nostalgic edits, and fun creations people actually remember.',
        'Create professional-looking visuals without hiring designers or expensive freelancers.',
        'Feel confident using AI tools even as a complete beginner.',
        'Save hours of frustration and start creating images that actually match the vision in your mind.',
        'Post AI visuals proudly instead of feeling embarrassed by average-looking results.',
    ]),
    ('Visual Proof', [
        'Real Outputs. Real Prompts. From This System.',
        'Action figures, Ghibli art, Mughal warriors, royal pet portraits, cinematic movie posters, and professional portraits — all from the same system, all for the same ₹299.',
        '6 styles shown. 1,100+ prompt templates across 11 books — many more styles inside.',
    ]),
    ('What’s Included', [
        '11 AI Style Books — Each book includes 100 ready-to-use prompts, so creating AI visuals never feels confusing or overwhelming.',
        'All 11 styles shown above — Action Figure, Ghibli, Nostalgia, Caricature, Headshots, Product, Cinematic, Scrapbook, Pet, Historical, and Trending Styles.',
        '1,100 Fill-in-the-Blank Prompt Templates — Just customize, paste, and generate. No technical prompting skills needed.',
        'AI Image Cheat Code Bonus Guide — Simple tricks and beginner-friendly guidance to improve results faster.',
        'Private Telegram Community Access — Get new ideas, trending styles, support, and inspiration regularly.',
        'Everything you need to go from beginner to confident creator.',
        'Total Value ₹2,189+',
        'Today’s Price: Just ₹299',
    ]),
    ('Perfect For', [
        'People who see stunning AI art online and want to create similar visuals themselves.',
        'Complete beginners who want amazing AI results without learning complicated prompting.',
        'Instagram creators who want scroll-stopping visuals people actually react to.',
        'Parents who want to create emotional and memorable AI images for their children and family.',
        'Freelancers and creators who want professional-looking visuals without hiring designers.',
        'Pet lovers who want to turn ordinary pet photos into creative AI artwork.',
        'Small business owners who want better-looking product photos and promotional visuals.',
        'Anyone who wants to create cool, impressive AI images for fun, gifts, content, or social media.',
    ]),
    ('Offer Stack', [
        'Unlocks the Full Prompt System + Bonuses.',
        'You’re getting a complete, proven system: every book, every bonus, and every future update.',
        'Book 1 — Action Figure & Toy Box — 100 prompts — ₹99 (was ₹199)',
        'Book 2 — Ghibli & Anime Style — 100 prompts — ₹99 (was ₹199)',
        'Book 3 — Childhood Nostalgia — 100 prompts — ₹99 (was ₹199)',
        'Book 4 — Caricature & Chibi — 100 prompts — ₹99 (was ₹199)',
        'Book 5 — Professional Headshots — 100 prompts — ₹99 (was ₹199)',
        'Book 6 — Product Photography — 100 prompts — ₹99 (was ₹199)',
        'Book 7 — Cinematic Movie Poster — 100 prompts — ₹99 (was ₹199)',
        'Book 8 — Vintage Scrapbook — 100 prompts — ₹99 (was ₹199)',
        'Book 9 — Pet Transformation — 100 prompts — ₹99 (was ₹199)',
        'Book 10 — Historical Time Travel — 100 prompts — ₹99 (was ₹199)',
        'Book 11 — Trending Styles — 100 prompts — ₹99 (was ₹199)',
        'Save ₹1,890 vs buying each book separately — plus 2 free bonuses. Early-access price: ₹299 → ₹499 when this window closes.',
        'BONUS #1 — AI Prompt Finder CustomGPT',
        'BONUS #2 — AI Image Cheat Code Ebook',
        'BONUS #3 — Private Telegram Community Access',
    ]),
    ('Pricing', [
        'One Price. No Subscription. Use It For Years.',
        'No subscription. No renewal. No price creep. Pay once — own it for life, including every new book added to the collection.',
        'Doing it manually — 10-50 hours. Researching styles, writing prompts, and fixing failed outputs from scratch.',
        'Hiring help — ₹2,000+. One designer session can cost far more than this full prompt library.',
        'This system — ₹299 once. 11 books, 1,100 templates, bonus resources, and future updates included.',
        'Single Book — One book of your choice, 100 fill-in-the-blank prompt templates, 6 personal variable slots per prompt, works with Midjourney, ChatGPT, Firefly, and DALL·E, interactive online viewer — no downloads, free bonus: The AI Image Cheat Code guide, Telegram community access, all future updates to your book.',
        'Full System Access — Save ₹1,890, all 11 books + bonus resources, lifetime access, every trending style covered, 6 personal variable slots per prompt, works with Midjourney, ChatGPT, Firefly, and DALL·E, interactive online viewer — no downloads, free bonus: The AI Image Cheat Code guide, exclusive Telegram community access, all future books automatically added.',
        'Support & Access: Instant Access After Payment, Works on Mobile & Laptop, Works with ChatGPT, Midjourney, Firefly & More, Beginner-Friendly, Lifetime Access, Future Updates Included.',
        'Early-access system pricing is currently ₹299. Individual books are listed separately at ₹199 each.',
        'Secure checkout via Razorpay (India)',
        '24-hour technical guarantee for access/login issues. No refunds after successful access.',
    ]),
    ('Testimonials', [
        'What Buyers Are Saying',
        'Most buyers were not AI experts. They were creators, parents, freelancers, and side-hustlers who wanted better results without hours of trial and error.',
        '“Posted my Ghibli portrait on Instagram. Got 146 likes in a day and a few DMs asking which app I used. I tell them I just filled in the template form. Most people don’t believe it took under a minute.” — Riya M., Mumbai.',
        '“Made my son’s action figure toy box in 10 mins. He cried happy tears. My wife shared it and three relatives messaged asking where to buy it. I made it myself. That reaction alone was worth 10x what I paid.” — Prashant K., Pune.',
        '“Using it for client work — headshots, movie posters, pet portraits. My clients think I have a full design team behind me. It’s just me and these 11 books. Genuinely worth every rupee.” — Anika S., Bangalore.',
        '“Opened an Etsy store after buying Book 7. In the first two weeks, I made around ₹1,150 from AI movie poster prints. A few buyers asked for custom versions, and I could deliver quickly. I recovered the system cost in the first few orders.” — Vikram T., Chennai.',
        '“Husband’s 40th was in 3 days, no gift idea. Bought the Scrapbook book, spent maybe 20 minutes turning old photos into a vintage album cover. He got emotional. My sister-in-law still asks where I ordered it from.” — Deepa R., Hyderabad.',
        '“I create Reels about AI tools. Used these prompts for a Midjourney demo Reel and gained 43 followers that week — better than my usual growth. Other creators asked which prompts I used. I just said I bought a prompt system. At ₹299 for all 11, it still felt like strong value.” — Suresh M., Delhi.',
    ]),
    ('Early Access Pricing', [
        'Right now, the complete 11-book system is available for just ₹299.',
        'Each book individually costs ₹199.',
        'Once the launch offer ends, the price increases.',
        'If AI art trends are already everywhere on Instagram and Reels, imagine where they’ll be 3 months from now.',
        'Best time to start creating is while the trends, styles, and tools are still fresh.',
        'Bonuses included free with the full system: AI Prompt Finder CustomGPT, bonus ebook, and private Telegram community access.',
    ]),
    ('More Styles', [
        'Still Not Sure? See What Else Is Possible.',
        'Pixar-style portraits, luxury product shots, Barbiecore fashion, childhood nostalgia, festive scrapbooks, and cyberpunk cities — all from this single ₹299 system.',
        'Still only 6 of 1,100+ styles. Every prompt works the same way — fill in the blanks, generate, done.',
    ]),
    ('FAQ', [
        'I have never used AI tools before. Will this still work for me? — Yes. The entire system is designed for complete beginners. Just pick a style, fill in a few details, and generate.',
        'Do I need to learn prompt engineering? — No. The prompts are already structured for you. No technical AI knowledge needed.',
        'Which AI tools does this work with? — It works with ChatGPT, Midjourney, Firefly, DALL·E, Ideogram, and most AI image generators.',
        'How much time does it take to create an image? — Most images can be created within a few minutes once the prompt is selected.',
        'Will I get instant access after payment? — Yes. Access details are delivered immediately after successful payment.',
        'Is this only for creators and designers? — Not at all. It is made for ordinary people, beginners, students, parents, freelancers, and anyone who wants to create impressive AI visuals.',
        'What exactly will I receive inside? — You get all 11 AI prompt books, 1,100 prompt templates, bonuses, community access, and future updates.',
        'Is the CustomGPT bonus included right now? — Yes. The AI Prompt Finder CustomGPT is currently included free with the system.',
        'Do I get support if I get stuck? — Yes. Buyers also get access to the Telegram community for guidance, updates, and support.',
        'Is there any refund policy? — Because this is a digital product with instant access, refunds are not available after access is delivered. However, if there is any technical issue with access, support will help resolve it quickly.',
    ]),
    ('Final CTA', [
        'Create Stunning AI Images Without Learning Prompting',
        '11 Premium AI Prompt Books',
        '1,100 Fill-in-the-Blank Templates',
        'Bonuses Included',
        'Lifetime Access',
        'Total Value: ₹2,189+',
        'Today Only: ₹299',
        'Instant email access after payment.',
    ]),
]

doc = Document()
doc.add_heading('AI Prompt Books Landing Page Copy (Section-wise)', level=1)
for index, (heading, items) in enumerate(sections):
    doc.add_heading(heading, level=2)
    if heading == 'Hero':
        doc.add_picture('landing-full.png', width=Inches(6.5))
        doc.add_paragraph('Screenshot: Full landing page view from the current static snapshot.')
    for item in items:
        if '—' in item or item.startswith('Save ') or item.startswith('BONUS') or item.startswith('Book '):
            doc.add_paragraph(item, style='List Bullet')
        else:
            doc.add_paragraph(item)
    if index < len(sections) - 1:
        doc.add_page_break()

doc.save('landing-page-copy-sectioned.docx')
print('landing-page-copy-sectioned.docx created')
