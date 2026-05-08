class AppConfig {
  static const apiBaseUrl = String.fromEnvironment(
    'API_BASE_URL',
    defaultValue: 'http://192.168.18.211:8001/api/v1',
  );

  static const storageBaseUrl = String.fromEnvironment(
    'STORAGE_BASE_URL',
    defaultValue: 'http://192.168.18.211:8001/storage',
  );

  static const appName = 'NOVA';
}
