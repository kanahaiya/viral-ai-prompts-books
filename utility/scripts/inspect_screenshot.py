from PIL import Image

path = 'landing-full.png'
with Image.open(path) as img:
    print('size', img.size)
    w, h = img.size
    segments = [('bottom', max(0, h - 2000)), ('mid', max(0, h - 4000)), ('top', 0)]
    for name, top in segments:
        bottom = min(h, top + 1200)
        out = f'crop_{name}.png'
        img.crop((0, top, w, bottom)).save(out)
        print(name, top, bottom, out)
