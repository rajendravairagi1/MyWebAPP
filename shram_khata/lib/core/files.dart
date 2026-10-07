import 'dart:io';

import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:path/path.dart' as p;
import 'package:path_provider/path_provider.dart';
import 'package:uuid/uuid.dart';

/// Lets the user take a photo or choose one, and copies it into the app's own
/// folder so it survives the gallery/cache being cleared. Returns the new path.
Future<String?> pickAndStoreImage(BuildContext context, {String folder = 'images'}) async {
  final source = await showModalBottomSheet<ImageSource>(
    context: context,
    builder: (ctx) => SafeArea(
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          ListTile(
            leading: const Icon(Icons.photo_camera_outlined),
            title: const Text('Take a photo'),
            onTap: () => Navigator.pop(ctx, ImageSource.camera),
          ),
          ListTile(
            leading: const Icon(Icons.photo_library_outlined),
            title: const Text('Choose from gallery'),
            onTap: () => Navigator.pop(ctx, ImageSource.gallery),
          ),
          const SizedBox(height: 8),
        ],
      ),
    ),
  );
  if (source == null) return null;
  final x = await ImagePicker().pickImage(source: source, maxWidth: 1800, imageQuality: 85);
  if (x == null) return null;
  return storeFile(File(x.path), folder: folder);
}

Future<String> storeFile(File source, {String folder = 'files'}) async {
  final docs = await getApplicationDocumentsDirectory();
  final dir = Directory(p.join(docs.path, folder));
  await dir.create(recursive: true);
  final ext = p.extension(source.path).isEmpty ? '.jpg' : p.extension(source.path);
  final dest = File(p.join(dir.path, '${const Uuid().v4()}$ext'));
  await source.copy(dest.path);
  return dest.path;
}

ImageProvider? fileImage(String? path) {
  if (path == null || path.isEmpty) return null;
  final f = File(path);
  return f.existsSync() ? FileImage(f) : null;
}
