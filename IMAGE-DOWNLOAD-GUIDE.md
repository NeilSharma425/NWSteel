# Image Download & Integration Guide

## Step-by-Step Instructions

### 1. Visit the Gallery Page
Open in your browser: **https://www.nwsteel.net/gallery.html**

### 2. Download These Images

From the gallery, download photos showing:

#### **Warehouse/Facility Photos** (Save to `images/warehouse/`)
- [ ] Exterior warehouse shots
- [ ] Interior warehouse with steel inventory
- [ ] Loading dock area
- [ ] Aerial view (if available)

**Suggested filenames:**
- `warehouse-exterior.jpg`
- `warehouse-interior.jpg`
- `warehouse-inventory.jpg`
- `facility-aerial.jpg`

#### **Equipment Photos** (Save to `images/equipment/`)
- [ ] Plasma cutting machine
- [ ] Saw/cutting equipment
- [ ] Shear machine
- [ ] Ironworker
- [ ] Delivery trucks

**Suggested filenames:**
- `plasma-cutter.jpg`
- `saw-machine.jpg`
- `shear-machine.jpg`
- `ironworker.jpg`
- `delivery-truck.jpg`

#### **Product Photos** (Save to `images/products/`)
- [ ] Steel beams/I-beams
- [ ] Steel plate stacks
- [ ] Pipe and tubing
- [ ] Angle iron
- [ ] General inventory shots

**Suggested filenames:**
- `steel-beams.jpg`
- `steel-plate.jpg`
- `steel-pipe.jpg`
- `steel-angles.jpg`
- `steel-inventory.jpg`

#### **Logo** (Save to `images/logos/`)
- [ ] Company logo (if visible on the site)

**Suggested filename:**
- `nwsteel-logo.png`

### 3. How to Save Images from Browser

**On Desktop:**
1. Right-click on an image
2. Select "Save image as..."
3. Navigate to the correct folder in your repository
4. Save with the suggested filename

**On Mobile/Tablet:**
1. Long-press on an image
2. Select "Download image" or "Save image"
3. Move to correct folder later

### 4. Upload to Repository

After downloading all images:

```bash
# Navigate to your repository
cd NWSteel

# Add all images to git
git add images/

# Commit the images
git commit -m "Add company photos from gallery"

# Push to GitHub
git push origin claude/gh-pages-uy7vi
```

### 5. Automatic Integration

Once images are in the correct folders, they will automatically display on:
- **Home Page**: Hero background, warehouse photos, equipment previews
- **About Page**: Company history images, facility photos
- **Services Page**: Equipment photos for each service
- **Inventory Page**: Product photos
- **Contact Page**: Facility location image

## Image Optimization (Optional but Recommended)

Before uploading, you can optimize images to reduce file size:

**Online Tools:**
- TinyPNG: https://tinypng.com
- Compressor.io: https://compressor.io
- Squoosh: https://squoosh.app

**Target File Sizes:**
- Hero images: < 500KB
- Equipment/product photos: < 300KB
- Logos: < 100KB

## Need Help?

If you have questions about:
- Which images to download → Download any warehouse, equipment, or product photos
- Where to place them → Follow the folder structure in `images/README.md`
- File naming → Use descriptive names (e.g., `plasma-cutter.jpg`, `warehouse-interior.jpg`)

The website will gracefully display SVG placeholders for any missing images, so you can add them gradually!
