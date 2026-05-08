# NOVA Flutter App

تطبيق Flutter احترافي لمتجر NOVA، مبني داخل مشروع Laravel الحالي ويرتبط بواجهات `/api/v1`.

## التشغيل

شغل Laravel API أولاً من جذر المشروع:

```powershell
php artisan serve --host=127.0.0.1 --port=8001
```

ثم شغل تطبيق الويب:

```powershell
cd nova_flutter_app
flutter run -d chrome --dart-define=API_BASE_URL=http://127.0.0.1:8001/api/v1
```

لتشغيله على Android emulator:

```powershell
flutter run --dart-define=API_BASE_URL=http://10.0.2.2:8001/api/v1
```

## التحقق

```powershell
flutter analyze
flutter test
```

## البنية

- `lib/src/core`: الثيم، الشبكة، التخزين، الراوتر، الودجت المشتركة.
- `lib/src/features`: auth, home, catalog, cart, checkout, orders, profile.
- الألوان مطابقة لهوية Nova Signature: كريمي، بنفسجي، وذهبي دافئ.
