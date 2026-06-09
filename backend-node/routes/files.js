const express = require('express');
const router = express.Router();
const upload = require('../middleware/upload');
const File = require('../models/File');

// route pour uploader un fichier
router.post('/upload', upload.single('image'), async (req, res) => {
  try {
    // sauvegarde dans MongoDB
    const fileData = new File({
      filename: req.file.filename,
      originalName: req.file.originalname,
      mimeType: req.file.mimetype,
      size: req.file.size,
      userId: req.body.userId,
      productId: req.body.productId
    });
    
    await fileData.save();
    
    res.json({
      message: 'Fichier uploadé avec succès',
      file: fileData
    });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

// route pour voir tous les fichiers
router.get('/', async (req, res) => {
  const files = await File.find().sort({ uploadDate: -1 });
  res.json(files);
});

// route pour télécharger un fichier
router.get('/:filename', (req, res) => {
  const filePath = `uploads/${req.params.filename}`;
  res.sendFile(filePath, { root: '.' });
});

module.exports = router;