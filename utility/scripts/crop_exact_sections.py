from PIL import Image

coords = {
    'landing-still_not_sure-crop.png': (0, 12972, 1184, 12972 + 1147),
    'landing-faq-crop.png': (0, 14119, 1184, 14119 + 996),
    'landing-final_cta-crop.png': (0, 15115, 1184, 15115 + 656),
}

with Image.open('landing-full.png') as img:
    for name, (left, top, right, bottom) in coords.items():
        crop = img.crop((left, top, right, bottom))
        crop.save(name)
        print('saved', name, crop.size)
