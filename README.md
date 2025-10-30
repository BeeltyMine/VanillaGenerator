
# VanillaGenerator

VanillaGenerator, BeeltyMine / PocketMine tabanlı sunucular için basit bir "vanilla world" (varsayılan Minecraft tarzı) oluşturucu ve yardımcı eklentidir. Bu eklenti, sunucu üzerinde hızlıca yeni bir vanilla dünya oluşturmak, temel ayarları yapılandırmak ve üretim işlemlerini otomatikleştirmek isteyen sunucu yöneticileri için tasarlanmıştır.

## Özellikler
- Yeni vanilla dünyalar oluşturma (seed ile veya rastgele)
- Hızlı kurulum — eklentiyi `plugins/` dizinine koyup sunucuyu yeniden başlatınca kullanılabilir
- Basit konfigürasyon ile varsayılan ayarları değiştirme
- Oluşturma/yenileme sırasında konsol ve sunucu loglarına bilgi yazma

> Not: Bu README eklentinin genel kullanımını açıklar. Eklentinin daha ileri özellikleri veya komutlar eklendiyse, lütfen ilgili `plugin.yml` veya kaynak kodu kontrol edin.

## Gereksinimler
- BeeltyMine / PocketMine-MP (stable branch ile uyumluluk hedeflenmiştir)
- PHP 8.x

## Kurulum
1. `VanillaGenerator` klasörünü `plugins/` dizinine kopyalayın (veya eklentinin PHAR/zip çıkışını kullanın).
2. Sunucuyu yeniden başlatın veya `plugins` dizinine eklentiyi koyduktan sonra `start` script'i ile sunucuyu başlatın.
3. Sunucu açılışında eklenti `plugins/` altında yüklenecek ve konsolda yükleme mesajı gösterilecektir.

## Hızlı Kullanım
This plugin can be configured to generate worlds automatically via your server's `pocketmine.yml` — you do not need to use plugin commands if you prefer configuration-based setup.

Add entries under the `worlds:` section of `pocketmine.yml` to select generator types for specific world folders. Example `pocketmine.yml` snippet:

```yaml
worlds:
  world:
    generator: vanilla_overworld # sets generator type of the world with folder name "world" to "vanilla_overworld"
  nether:
    generator: vanilla_nether
```

With the above, when the server loads or when the world is created by the server, the specified generator will be used for those world folders. If you want command-based generation instead, check `plugin.yml` for available commands; otherwise, configure `pocketmine.yml` as shown.

## Yapılandırma
- Eklentinin kendi ayar dosyası varsa `plugins/VanillaGenerator/config.yml` veya benzeri bir yerde bulunur. Varsayılan ayarları değiştirmek için bu dosyayı düzenleyin ve sunucuyu yeniden başlatın.

Örnek (varsayımsal) config.yml:

```
default_world_type: "vanilla"
generate_structures: true
default_seed: null # null => rastgele
```

## Loglar ve Hata Ayıklama
- Oluşturma/üretim süreçleri konsola ve `plugins/VanillaGenerator/logs/` altına yazılır (eğer eklenti log dizini oluşturuyorsa).
- Eğer dünya oluşturma sırasında başarısız oluyorsa:
	1. Sunucu konsolundaki hata mesajını kontrol edin.
 2. `plugins/VanillaGenerator` altındaki log dosyalarını inceleyin.
 3. Yeterli disk alanı ve yazma izinlerini doğrulayın.

## Güvenlik ve Önlemler
- `vdelete` veya benzeri kalıcı veri silen komutları kullanmadan önce mutlaka yedek alın.
- Bu eklenti yalnızca sunucu tarafında çalışır; istemcilerde herhangi bir değişiklik yapmaz.

## Katkıda Bulunma
- Hatalar, istekler veya iyileştirmeler için pull request veya issue açabilirsiniz. Lütfen değişikliklerinizi küçük, test edilebilir parçalar halinde gönderin.

## Lisans
- Eklenti ana projede kullanılan lisans ile uyumlu olacaktır. (Repo kökünde bulunan `LICENSE` dosyasına bakınız.)

---
Eğer README'de görmek istediğiniz özel bir bölüm (örneğin komut listesi, gerçek config alanları veya örnek seed'ler) varsa söyleyin; içeriği güncelleyeyim.

