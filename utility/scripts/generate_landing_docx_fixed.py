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
    ('This Is Perfect For', [
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
    ('Missing Bottom Sections', [
        'Still Not Sure? See What Else Is Possible.',
        'Pixar-style portraits, luxury product shots, Barbiecore fashion, childhood nostalgia, festive scrapbooks, and cyberpunk cities — all from this single ₹299 system.',
        'Still on the Fence? These Questions Are For You.',
        'Create Stunning AI Images Without Learning Prompting',
    ]),
]

doc = Document()
doc.add_heading('AI Prompt Books Landing Page Copy (Fixed Sectioned Version)', level=1)
for heading, items in sections:
    doc.add_heading(heading, level=2)
    for item in items:
        doc.add_paragraph(item)
    if heading == 'Pricing':
        doc.add_paragraph('Screenshot: confirmed lower page sections are present below.')
        doc.add_picture('landing-still_not_sure-crop.png', width=Inches(6.5))
        doc.add_paragraph('Still Not Sure? section shown above.')
        doc.add_picture('landing-faq-crop.png', width=Inches(6.5))
        doc.add_paragraph('FAQ section shown above.')
        doc.add_picture('landing-final_cta-crop.png', width=Inches(6.5))
        doc.add_paragraph('Final CTA section shown above.')
    doc.add_page_break()

out_file = 'landing-page-copy-fixed.docx'
doc.save(out_file)
print(out_file + ' created')
