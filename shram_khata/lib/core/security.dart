import 'dart:convert';
import 'dart:math';

import 'package:crypto/crypto.dart';

/// Salted, stretched hashing for the on-device password / PIN.
/// This protects against casual snooping on a shared phone. Real account
/// security (email verification, password reset) comes from the cloud
/// backend once it is connected.
class Security {
  Security._();

  static String newSalt() {
    final r = Random.secure();
    return base64Url.encode(List<int>.generate(16, (_) => r.nextInt(256)));
  }

  static String hash(String secret, String salt) {
    List<int> bytes = utf8.encode('$salt:$secret');
    for (var i = 0; i < 5000; i++) {
      bytes = sha256.convert(bytes).bytes;
    }
    return base64Url.encode(bytes);
  }

  static bool verify(String secret, String salt, String expected) =>
      hash(secret, salt) == expected;

  static final _email = RegExp(r'^[^@\s]+@[^@\s]+\.[^@\s]{2,}$');
  static bool isEmail(String s) => _email.hasMatch(s.trim());
}
