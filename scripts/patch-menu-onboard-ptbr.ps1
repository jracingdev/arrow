# Patch menu keys + onboarding EN->PT-BR (exact string replace)
$ErrorActionPreference = 'Stop'
$root = 'D:\arrow'
$utf8 = New-Object System.Text.UTF8Encoding $false

# --- 1) Overlay crítico no lang.php admin/store/website pt_br ---
$menuOverlay = @{
  "'content_management' => 'Content Management'" = "'content_management' => 'Gestão de Conteúdo'"
  "'datatable_info' => 'Showing _START_ to _END_ of _TOTAL_ entries'" = "'datatable_info' => 'Mostrando _START_ a _END_ de _TOTAL_ registros'"
  "'datatable_info_empty' => 'Showing 0 to 0 of 0 entries'" = "'datatable_info_empty' => 'Mostrando 0 a 0 de 0 registros'"
  "'datatable_info_filtered' => '(filtered from _MAX_ total entries)'" = "'datatable_info_filtered' => '(filtrado de _MAX_ registros no total)'"
  "'datatable_length_menu' => 'Show _MENU_ entries'" = "'datatable_length_menu' => 'Mostrar _MENU_ registros'"
  "'search_menu' => 'Search Menu'" = "'search_menu' => 'Pesquisar menu'"
  "'menu_items' => `"Banner Items`"" = "'menu_items' => 'Itens de banner'"
  "'cms_plural' => 'CMS Pages'" = "'cms_plural' => 'Páginas CMS'"
  "'on_board_plural' => 'On Boarding Screens'" = "'on_board_plural' => 'Telas de onboarding'"
  "'on_board_table' => 'On Boarding List'" = "'on_board_table' => 'Lista de onboarding'"
  "'on_board_details' => 'On Boarding details'" = "'on_board_details' => 'Detalhes do onboarding'"
  "'on_board_table_text' => 'View and manage all the on board screens'" = "'on_board_table_text' => 'Veja e gerencie todas as telas de onboarding'"
  "'onboarding_info' => 'Onboarding Info'" = "'onboarding_info' => 'Informações do onboarding'"
  "'app_screen' => 'App Screen'" = "'app_screen' => 'Tela do aplicativo'"
  "'access_management' => 'ACCESS MANAGEMENT'" = "'access_management' => 'GESTÃO DE ACESSO'"
  "'vendor_management' => 'VENDOR MANAGEMENT'" = "'vendor_management' => 'GESTÃO DE LOJISTAS'"
  "'live_monitoring' => 'LIVE MONITORING'" = "'live_monitoring' => 'MONITORAMENTO AO VIVO'"
  "'store_and_driver_management' => 'STORE & DRIVER MANAGEMENT'" = "'store_and_driver_management' => 'GESTÃO DE LOJAS E MOTORISTAS'"
  "'store_management' => 'STORE MANAGEMENT'" = "'store_management' => 'GESTÃO DE LOJAS'"
  "'driver_management' => 'DRIVER MANAGEMENT'" = "'driver_management' => 'GESTÃO DE MOTORISTAS'"
  "'category_and_items_management' => 'CATEGORY & ITEMS MANAGEMENT'" = "'category_and_items_management' => 'GESTÃO DE CATEGORIAS E ITENS'"
  "'brand_management' => 'BRAND MANAGEMENT'" = "'brand_management' => 'GESTÃO DE MARCAS'"
  "'destination_management' => 'DESTINATION MANAGEMENT'" = "'destination_management' => 'GESTÃO DE DESTINOS'"
  "'order_and_promotions_management' => 'ORDER & PROMOTIONS MANAGEMENT'" = "'order_and_promotions_management' => 'GESTÃO DE PEDIDOS E PROMOÇÕES'"
  "'document_management' => 'DOCUMENT MANAGEMENT'" = "'document_management' => 'GESTÃO DE DOCUMENTOS'"
  "'notification_management' => 'NOTIFICATION MANAGEMENT'" = "'notification_management' => 'GESTÃO DE NOTIFICAÇÕES'"
  "'design_and_content_management' => 'DESIGN AND CONTENT MANAGEMENT'" = "'design_and_content_management' => 'GESTÃO DE DESIGN E CONTEÚDO'"
  "'settings_and_configurations' => 'SETTINGS AND CONFIGURATIONS'" = "'settings_and_configurations' => 'CONFIGURAÇÕES'"
  "'payment_and_transactions' => 'PAYMENT AND TRANSACTIONs'" = "'payment_and_transactions' => 'PAGAMENTOS E TRANSAÇÕES'"
  "'report_and_analytics' => 'REPORT AND ANALYTICS'" = "'report_and_analytics' => 'RELATÓRIOS E ANALYTICS'"
  "'business_setup' => 'BUSINESS SETUP'" = "'business_setup' => 'CONFIGURAÇÃO DO NEGÓCIO'"
  "'business_setup' => 'CONFIGURAÇÃO DO NEGÓCIO'" = "'business_setup' => 'CONFIGURAÇÃO DO NEGÓCIO'"
  "'ondemand_services_management' => 'ON-DEMAND SERVICE MANAGEMENT'" = "'ondemand_services_management' => 'GESTÃO DE SERVIÇOS SOB DEMANDA'"
  "'cab_services_management' => 'CAB SERVICE MANAGEMENT'" = "'cab_services_management' => 'GESTÃO DE CORRIDAS'"
  "'parcel_services_management' => 'PARCEL SERVICE MANAGEMENT'" = "'parcel_services_management' => 'GESTÃO DE ENCOMENDAS'"
  "'rental_services_management' => 'RENTAL SERVICE MANAGEMENT'" = "'rental_services_management' => 'GESTÃO DE ALUGUEL'"
  "'multivendor_services_management' => 'MULTIVENDOR SERVICE MANAGEMENT'" = "'multivendor_services_management' => 'GESTÃO MULTIVENDOR'"
  "'ecommerce_services_management' => 'ECOMMERCE SERVICE MANAGEMENT'" = "'ecommerce_services_management' => 'GESTÃO E-COMMERCE'"
  "'other_services_management' => 'OTHER SERVICE MANAGEMENT'" = "'other_services_management' => 'GESTÃO DE OUTROS SERVIÇOS'"
  "'owner_and_fleet_management' => 'OWNER & FLEET MANAGEMENT'" = "'owner_and_fleet_management' => 'GESTÃO DE PROPRIETÁRIOS E FROTA'"
  "'disbursement_management' => 'DISBURSEMENT MANAGEMENT'" = "'disbursement_management' => 'GESTÃO DE REPASSES'"
  "'ecommerce_multivendor_management' => 'ECOMMERCE / MULTIVENDOR MANAGEMENT'" = "'ecommerce_multivendor_management' => 'GESTÃO E-COMMERCE / MARKETPLACE'"
  "'tax_setting' => 'Tax Settings'" = "'tax_setting' => 'Configurações de imposto'"
  "'tax_settings' => 'Tax Settings'" = "'tax_settings' => 'Configurações de imposto'"
  "'reports_tax' => 'Tax Report'" = "'reports_tax' => 'Relatório de impostos'"
  "'reports_sale' => 'Sales Report'" = "'reports_sale' => 'Relatório de vendas'"
  "'vat_tax' => 'VAT/TAX'" = "'vat_tax' => 'Imposto/Taxa'"
  "'vat_setting' => `"VAT Setting`"" = "'vat_setting' => 'Configuração de imposto'"
  "'tax_plural' => 'Taxes'" = "'tax_plural' => 'Impostos'"
  "'total_tax' => 'Total Tax'" = "'total_tax' => 'Imposto total'"
  "'point_of_sale' => 'Point Of Sale'" = "'point_of_sale' => 'Ponto de venda'"
  "'dashboard' => 'Dashboard'" = "'dashboard' => 'Painel'"
  "'dashboard' => 'Painel'" = "'dashboard' => 'Painel'"
  "'loading' => 'Loading...'" = "'loading' => 'Carregando...'"
  "'processing' => 'Processing'" = "'processing' => 'Processando'"
  "'search' => 'Search'" = "'search' => 'Pesquisar'"
  "'actions' => 'Actions'" = "'actions' => 'Ações'"
  "'description' => 'Description'" = "'description' => 'Descrição'"
  "'save' => 'Save'" = "'save' => 'Salvar'"
  "'edit' => 'Edit'" = "'edit' => 'Editar'"
  "'delete' => 'Delete'" = "'delete' => 'Excluir'"
  "'help_support' => 'Help & Support'" = "'help_support' => 'Ajuda e suporte'"
}

foreach ($panel in @('admin','store','website')) {
  $path = Join-Path $root "web\$panel\resources\lang\pt_br\lang.php"
  if (-not (Test-Path $path)) { continue }
  $txt = [IO.File]::ReadAllText($path, $utf8)
  foreach ($k in $menuOverlay.Keys) {
    if ($txt.Contains($k)) { $txt = $txt.Replace($k, $menuOverlay[$k]) }
  }
  # Já traduzidas parcialmente com encoding ok — reforçar chaves críticas mesmo se valor atual diferente
  $forced = @{
    'content_management' = 'Gestão de Conteúdo'
    'datatable_info' = 'Mostrando _START_ a _END_ de _TOTAL_ registros'
    'datatable_info_empty' = 'Mostrando 0 a 0 de 0 registros'
    'datatable_info_filtered' = '(filtrado de _MAX_ registros no total)'
    'datatable_length_menu' = 'Mostrar _MENU_ registros'
    'search_menu' = 'Pesquisar menu'
    'on_board_plural' = 'Telas de onboarding'
    'on_board_table' = 'Lista de onboarding'
    'on_board_details' = 'Detalhes do onboarding'
    'on_board_table_text' = 'Veja e gerencie todas as telas de onboarding'
    'onboarding_info' = 'Informações do onboarding'
    'app_screen' = 'Tela do aplicativo'
    'menu_items' = 'Itens de banner'
    'cms_plural' = 'Páginas CMS'
    'access_management' = 'GESTÃO DE ACESSO'
    'vendor_management' = 'GESTÃO DE LOJISTAS'
    'live_monitoring' = 'MONITORAMENTO AO VIVO'
    'store_and_driver_management' = 'GESTÃO DE LOJAS E MOTORISTAS'
    'store_management' = 'GESTÃO DE LOJAS'
    'driver_management' = 'GESTÃO DE MOTORISTAS'
    'category_and_items_management' = 'GESTÃO DE CATEGORIAS E ITENS'
    'brand_management' = 'GESTÃO DE MARCAS'
    'destination_management' = 'GESTÃO DE DESTINOS'
    'order_and_promotions_management' = 'GESTÃO DE PEDIDOS E PROMOÇÕES'
    'document_management' = 'GESTÃO DE DOCUMENTOS'
    'notification_management' = 'GESTÃO DE NOTIFICAÇÕES'
    'design_and_content_management' = 'GESTÃO DE DESIGN E CONTEÚDO'
    'settings_and_configurations' = 'CONFIGURAÇÕES'
    'payment_and_transactions' = 'PAGAMENTOS E TRANSAÇÕES'
    'report_and_analytics' = 'RELATÓRIOS E ANALYTICS'
    'business_setup' = 'CONFIGURAÇÃO DO NEGÓCIO'
    'ondemand_services_management' = 'GESTÃO DE SERVIÇOS SOB DEMANDA'
    'cab_services_management' = 'GESTÃO DE CORRIDAS'
    'parcel_services_management' = 'GESTÃO DE ENCOMENDAS'
    'rental_services_management' = 'GESTÃO DE ALUGUEL'
    'owner_and_fleet_management' = 'GESTÃO DE PROPRIETÁRIOS E FROTA'
    'disbursement_management' = 'GESTÃO DE REPASSES'
    'tax_setting' = 'Configurações de imposto'
    'tax_settings' = 'Configurações de imposto'
    'reports_tax' = 'Relatório de impostos'
    'vat_tax' = 'Imposto/Taxa'
    'point_of_sale' = 'Ponto de venda'
  }
  foreach ($key in $forced.Keys) {
    $pat = "(?m)('$key'\s*=>\s*)(['""])(.*?)(\2)"
    $val = $forced[$key].Replace('\','\\').Replace("'","\'")
    $txt = [regex]::Replace($txt, $pat, { param($m) $m.Groups[1].Value + "'" + $val + "'" })
  }
  [IO.File]::WriteAllText($path, $txt, $utf8)
  Write-Host "Patched $path"

  # Alias Laravel pt_BR (case) apontando para mesmos arquivos
  $ptBR = Join-Path $root "web\$panel\resources\lang\pt_BR"
  if (-not (Test-Path $ptBR)) {
    New-Item -ItemType Directory -Path $ptBR -Force | Out-Null
  }
  Copy-Item (Join-Path $root "web\$panel\resources\lang\pt_br\*") $ptBR -Force
  Write-Host "Synced pt_BR alias for $panel"
}

# --- 2) Onboarding exact replacements no collections.json ---
$onboardMap = [ordered]@{
  'Your All-in-One Work App.' = 'Seu app de trabalho completo'
  'Your All-in-One Work App' = 'Seu app de trabalho completo'
  'See booking details, update status, and chat with ease. Arrow Worker -Available in multiple languages.' = 'Veja detalhes das reservas, atualize o status e converse com facilidade. Arrow Worker — disponível em vários idiomas.'
  'Start Serving Customers' = 'Comece a atender clientes'
  'Track orders, manage inventory, and grow your revenue from day one.' = 'Acompanhe pedidos, gerencie o estoque e aumente seu faturamento desde o primeiro dia.'
  'Quick and Easy Setup' = 'Configuração rápida e fácil'
  'Provide your details, verify documents, and you''re ready to hit the road.' = 'Informe seus dados, verifique os documentos e esteja pronto para começar.'
  'Provide your details, verify documents, and youâ€™re ready to hit the road.' = 'Informe seus dados, verifique os documentos e esteja pronto para começar.'
  'Welcome to Arrow!' = 'Bem-vindo à Arrow!'
  'Partner with us to reach more customers and grow your business effortlessly.' = 'Seja nosso parceiro para alcançar mais clientes e fazer seu negócio crescer com facilidade.'
  'Welcome to Arrow Worker!' = 'Bem-vindo ao Arrow Worker!'
  'Manage your work schedule, view bookings, and update their status - all in one place.' = 'Gerencie sua agenda, veja as reservas e atualize o status — tudo em um só lugar.'
  'Welcome to Arrow Provider!' = 'Bem-vindo ao Arrow Prestador!'
  'Manage your business, workers and services - all in one place.' = 'Gerencie seu negócio, profissionais e serviços — tudo em um só lugar.'
  "From Shopping to Rides, We've Got You Covered" = 'Das compras às corridas, a Arrow cobre você'
  'Manage vendors, orders, bookings and transactions with an easy-to-use interface.' = 'Gerencie lojas, pedidos, reservas e transações com uma interface fácil de usar.'
  'Work Simplified Globally.' = 'Trabalho simplificado'
  'Work Simplified Globally' = 'Trabalho simplificado'
  'Manage bookings, chat in multiple languages, and get things done efficiently with Arrow Worker.' = 'Gerencie reservas, converse em vários idiomas e resolva tudo com eficiência no Arrow Worker.'
  'Welcome to the community' = 'Bem-vindo à comunidade'
  'Join our network and start earning with flexible hours and seamless deliveries.' = 'Entre na nossa rede e comece a ganhar com horários flexíveis e entregas simples.'
  'Start Driving & Earning' = 'Comece a dirigir e ganhar'
  'Get instant access to orders and navigate efficiently with our built-in tools.' = 'Tenha acesso imediato aos pedidos e navegue com eficiência com nossas ferramentas.'
}

$colPath = Join-Path $root 'firebase\import-export\collections.json'
$col = [IO.File]::ReadAllText($colPath, $utf8)
foreach ($k in $onboardMap.Keys) {
  if ($col.Contains($k)) {
    $col = $col.Replace($k, [string]$onboardMap[$k])
    Write-Host ("Replaced: " + $k.Substring(0, [Math]::Min(50,$k.Length)))
  } else {
    Write-Host ("MISS: " + $k.Substring(0, [Math]::Min(50,$k.Length)))
  }
}
[IO.File]::WriteAllText($colPath, $col, $utf8)
Write-Host 'Updated collections.json'
Write-Host DONE
