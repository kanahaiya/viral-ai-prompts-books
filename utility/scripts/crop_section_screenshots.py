from PIL import Image

with Image.open('landing-full.png') as img:
    w, h = img.size
    areas = {
        'still_not_sure': (0, 12800, w, 13800),
        'faq': (0, 14100, w, 15100),
        'final_cta': (0, 15050, w, 16050)
    }
    for name, coords in areas.items():
        crop = img.crop(coords)
        crop.save(f'landing-{name}-crop.png')
        print(name, coords)
