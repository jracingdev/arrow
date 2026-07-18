# Gera resources/lang/pt_br a partir de en (slug admin: pt_br)
$ErrorActionPreference = 'Stop'
$root = 'D:\arrow'

function New-Dict {
  $d = New-Object 'System.Collections.Generic.Dictionary[string,string]' ([StringComparer]::Ordinal)
  foreach ($pair in $args) {
    if (-not $d.ContainsKey($pair[0])) { $d[$pair[0]] = $pair[1] }
  }
  return $d
}

# Frases exatas EN -> PT-BR
$exactPairs = @(
  @('Showing _START_ to _END_ of _TOTAL_ entries', 'Mostrando _START_ a _END_ de _TOTAL_ registros'),
  @('Showing 0 to 0 of 0 entries', 'Mostrando 0 a 0 de 0 registros'),
  @('(filtered from _MAX_ total entries)', '(filtrado de _MAX_ registros no total)'),
  @('Show _MENU_ entries', 'Mostrar _MENU_ registros'),
  @('Content Management', 'Gestão de Conteúdo'),
  @('DESIGN AND CONTENT MANAGEMENT', 'GESTÃO DE DESIGN E CONTEÚDO'),
  @('These credentials do not match our records.', 'Essas credenciais não correspondem aos nossos registros.'),
  @('The provided password is incorrect.', 'A senha informada está incorreta.'),
  @('Too many login attempts. Please try again in :seconds seconds.', 'Muitas tentativas de login. Tente novamente em :seconds segundos.'),
  @('Your password has been reset!', 'Sua senha foi redefinida!'),
  @('We have emailed your password reset link!', 'Enviamos o link de redefinição de senha por e-mail!'),
  @('Please wait before retrying.', 'Aguarde antes de tentar novamente.'),
  @('This password reset token is invalid.', 'Este token de redefinição de senha é inválido.'),
  @("We can't find a user with that email address.", 'Não encontramos um usuário com esse endereço de e-mail.'),
  @('&laquo; Previous', '&laquo; Anterior'),
  @('Next &raquo;', 'Próximo &raquo;'),
  @('Actions', 'Ações'),
  @('Active', 'Ativo'),
  @('Inactive', 'Inativo'),
  @('Add size', 'Adicionar tamanho'),
  @('Add New Option', 'Adicionar Nova Opção'),
  @('Add Option', 'Adicionar Opção'),
  @('Admin Configurations', 'Configurações do Admin'),
  @('Admin Commission', 'Comissão do Admin'),
  @('App Management', 'Gestão do App'),
  @('Settings', 'Configurações'),
  @('Print Booking', 'Imprimir Reserva'),
  @('Edit Booking', 'Editar Reserva'),
  @('Application Name', 'Nome do Aplicativo'),
  @('Short description', 'Descrição curta'),
  @('Global Settings', 'Configurações Globais'),
  @('Payment Methods', 'Métodos de Pagamento'),
  @('Push Notifications', 'Notificações Push'),
  @('Mobile Application Setting', 'Configuração do Aplicativo Móvel'),
  @('Social Authentication', 'Autenticação Social'),
  @('Save', 'Salvar'),
  @('Cancel', 'Cancelar'),
  @('Delete', 'Excluir'),
  @('Edit', 'Editar'),
  @('Create', 'Criar'),
  @('Update', 'Atualizar'),
  @('Search', 'Pesquisar'),
  @('Loading...', 'Carregando...'),
  @('Processing', 'Processando'),
  @('Please wait', 'Por favor, aguarde'),
  @('Error', 'Erro'),
  @('Warning', 'Aviso'),
  @('Success', 'Sucesso'),
  @('Yes', 'Sim'),
  @('No', 'Não'),
  @('Status', 'Status'),
  @('Name', 'Nome'),
  @('Email', 'E-mail'),
  @('Phone', 'Telefone'),
  @('Address', 'Endereço'),
  @('Password', 'Senha'),
  @('Confirm Password', 'Confirmar Senha'),
  @('First Name', 'Nome'),
  @('Last Name', 'Sobrenome'),
  @('Mobile Number', 'Celular'),
  @('User', 'Usuário'),
  @('Users', 'Usuários'),
  @('Order', 'Pedido'),
  @('Orders', 'Pedidos'),
  @('Delivery', 'Entrega'),
  @('Driver', 'Motorista'),
  @('Drivers', 'Motoristas'),
  @('Store', 'Loja'),
  @('Stores', 'Lojas'),
  @('Vendor', 'Lojista'),
  @('Vendors', 'Lojistas'),
  @('Customer', 'Cliente'),
  @('Customers', 'Clientes'),
  @('Invoice', 'Fatura'),
  @('Payment', 'Pagamento'),
  @('Wallet', 'Carteira'),
  @('Dashboard', 'Painel'),
  @('Profile', 'Perfil'),
  @('Logout', 'Sair'),
  @('Login', 'Entrar'),
  @('Admin Login', 'Login do Admin'),
  @('Total Tax', 'Imposto Total'),
  @('Total Tax Amount', 'Valor Total do Imposto'),
  @('Taxes', 'Impostos'),
  @('VAT Setting', 'Configuração de Imposto'),
  @('VAT/TAX', 'Imposto/Taxa'),
  @('Tax', 'Imposto'),
  @('Currency', 'Moeda'),
  @('Language', 'Idioma'),
  @('Languages', 'Idiomas'),
  @('Zone', 'Zona'),
  @('Zones', 'Zonas'),
  @('Category', 'Categoria'),
  @('Categories', 'Categorias'),
  @('Product', 'Produto'),
  @('Products', 'Produtos'),
  @('Item', 'Item'),
  @('Items', 'Itens'),
  @('Discount', 'Desconto'),
  @('Coupon', 'Cupom'),
  @('Coupons', 'Cupons'),
  @('Notification', 'Notificação'),
  @('Notifications', 'Notificações'),
  @('Document', 'Documento'),
  @('Documents', 'Documentos'),
  @('Home', 'Início'),
  @('Back', 'Voltar'),
  @('Next', 'Próximo'),
  @('Previous', 'Anterior'),
  @('First', 'Primeiro'),
  @('Last', 'Último'),
  @('Show', 'Mostrar'),
  @('entries', 'registros'),
  @('Showing', 'Mostrando'),
  @('No data found.', 'Nenhum dado encontrado.'),
  @('No Reviews Found', 'Nenhuma avaliação encontrada'),
  @('View All', 'Ver Tudo'),
  @('Add More', 'Adicionar Mais'),
  @('Filter By', 'Filtrar Por'),
  @('Apply Filter', 'Aplicar Filtro'),
  @('Clear Filter', 'Limpar Filtro'),
  @('Menu', 'Menu'),
  @('Permission', 'Permissão'),
  @('Search Menu', 'Pesquisar Menu'),
  @('Verified', 'Verificado'),
  @('Rejected', 'Rejeitado'),
  @('Pending', 'Pendente'),
  @('Completed', 'Concluído'),
  @('Cancelled', 'Cancelado'),
  @('Canceled', 'Cancelado'),
  @('Accepted', 'Aceito'),
  @('Order Placed', 'Pedido Realizado'),
  @('Order Accepted', 'Pedido Aceito'),
  @('Order Completed', 'Pedido Concluído'),
  @('Order Rejected', 'Pedido Recusado'),
  @('Driver Accepted', 'Motorista Aceitou'),
  @('In Transit', 'Em Trânsito'),
  @('Delivered', 'Entregue'),
  @('Enable COD', 'Ativar Pagamento na Entrega'),
  @('Check it to enable COD payment method', 'Marque para ativar o pagamento na entrega'),
  @('Log in to your panel to proceed', 'Entre no seu painel para continuar'),
  @('BUSINESS SETUP', 'CONFIGURAÇÃO DO NEGÓCIO'),
  @('Business Setup', 'Configuração do Negócio'),
  @('REPORT AND ANALYTICS', 'RELATÓRIOS E ANALYTICS'),
  @('ORDER & PROMOTIONS MANAGEMENT', 'GESTÃO DE PEDIDOS E PROMOÇÕES'),
  @('DOCUMENT MANAGEMENT', 'GESTÃO DE DOCUMENTOS'),
  @('NOTIFICATION MANAGEMENT', 'GESTÃO DE NOTIFICAÇÕES'),
  @('SETTINGS AND CONFIGURATIONS', 'CONFIGURAÇÕES'),
  @('PAYMENT AND TRANSACTIONs', 'PAGAMENTOS E TRANSAÇÕES'),
  @('STORE & DRIVER MANAGEMENT', 'GESTÃO DE LOJAS E MOTORISTAS'),
  @('STORE MANAGEMENT', 'GESTÃO DE LOJAS'),
  @('DRIVER MANAGEMENT', 'GESTÃO DE MOTORISTAS'),
  @('CATEGORY & ITEMS MANAGEMENT', 'GESTÃO DE CATEGORIAS E ITENS'),
  @('ECOMMERCE / MULTIVENDOR MANAGEMENT', 'GESTÃO E-COMMERCE / MARKETPLACE'),
  @('Modules Section', 'Seção de Módulos'),
  @('Select Module & Monitor your business module wise', 'Selecione o módulo e monitore seu negócio por módulo'),
  @('Order Status overview', 'Visão geral do status dos pedidos'),
  @('Quick insight into all ongoing and completed orders', 'Visão rápida de todos os pedidos em andamento e concluídos'),
  @('Quick insight into all sales', 'Visão rápida de todas as vendas'),
  @('Quick insight into all sales Overview', 'Visão geral rápida de todas as vendas'),
  @('Expand by adding new modules as your business grows.', 'Expanda adicionando novos módulos conforme seu negócio cresce.'),
  @('Sort ascending', 'Ordenar crescente'),
  @('Sort descending', 'Ordenar decrescente'),
  @('January', 'Janeiro'),
  @('February', 'Fevereiro'),
  @('March', 'Março'),
  @('April', 'Abril'),
  @('May', 'Maio'),
  @('June', 'Junho'),
  @('July', 'Julho'),
  @('August', 'Agosto'),
  @('September', 'Setembro'),
  @('October', 'Outubro'),
  @('November', 'Novembro'),
  @('December', 'Dezembro'),
  @('No record found', 'Nenhum registro encontrado'),
  @('Processing your request', 'Processando sua solicitação'),
  @('NOTE : Please Click on Edit Button After Making Changes, Otherwise Data may not Save', 'NOTA: Clique em Editar após fazer alterações, caso contrário os dados podem não ser salvos'),
  @('Address line1', 'Endereço linha 1'),
  @('Postal Code', 'CEP'),
  @('Zip Code', 'CEP'),
  @('Zipcode', 'CEP'),
  @('Tax ID', 'CNPJ/CPF'),
  @('TIN/VAT', 'CNPJ'),
  @('TIN', 'CNPJ'),
  @('The :attribute must be accepted.', 'O campo :attribute deve ser aceito.'),
  @('The :attribute must be a valid email address.', 'O campo :attribute deve ser um endereço de e-mail válido.'),
  @('The :attribute field is required.', 'O campo :attribute é obrigatório.'),
  @('The selected :attribute is invalid.', 'O :attribute selecionado é inválido.'),
  @('The :attribute confirmation does not match.', 'A confirmação de :attribute não confere.'),
  @('The password is incorrect.', 'A senha está incorreta.')
)

$partialPairs = @(
  @('Save ', 'Salvar '),
  @('Delete ', 'Excluir '),
  @('Create ', 'Criar '),
  @('Update ', 'Atualizar '),
  @('Add ', 'Adicionar '),
  @('Remove ', 'Remover '),
  @('Enable ', 'Ativar '),
  @('Disable ', 'Desativar '),
  @('Select ', 'Selecionar '),
  @('Please enter', 'Informe'),
  @('Please select', 'Selecione'),
  @('Please ', 'Por favor, '),
  @('successfully', 'com sucesso'),
  @('Successfully', 'Com sucesso'),
  @('Management', 'Gestão'),
  @('Settings', 'Configurações'),
  @('Password', 'Senha'),
  @('Mobile Number', 'Celular'),
  @('Mobile', 'Celular'),
  @('Users', 'Usuários'),
  @('User ', 'Usuário '),
  @('Orders', 'Pedidos'),
  @('Order ', 'Pedido '),
  @('Delivery', 'Entrega'),
  @('Drivers', 'Motoristas'),
  @('Driver', 'Motorista'),
  @('Stores', 'Lojas'),
  @('Store ', 'Loja '),
  @('Vendors', 'Lojistas'),
  @('Vendor', 'Lojista'),
  @('Customers', 'Clientes'),
  @('Customer', 'Cliente'),
  @('Invoice', 'Fatura'),
  @('Payment', 'Pagamento'),
  @('Wallet', 'Carteira'),
  @('Dashboard', 'Painel'),
  @('Notification', 'Notificação'),
  @('Document', 'Documento'),
  @('Category', 'Categoria'),
  @('Product', 'Produto'),
  @('Discount', 'Desconto'),
  @('Coupon', 'Cupom'),
  @('Commission', 'Comissão'),
  @('Amount', 'Valor'),
  @('Currency', 'Moeda'),
  @('Language', 'Idioma'),
  @('VAT', 'Imposto'),
  @('Tax', 'Imposto'),
  @('Search', 'Pesquisar'),
  @('Loading', 'Carregando'),
  @('Description', 'Descrição'),
  @('Address', 'Endereço'),
  @('Location', 'Localização'),
  @('Pending', 'Pendente'),
  @('Approved', 'Aprovado'),
  @('Rejected', 'Rejeitado'),
  @('Completed', 'Concluído'),
  @('Cancelled', 'Cancelado'),
  @('Canceled', 'Cancelado'),
  @('Accepted', 'Aceito'),
  @('Active', 'Ativo'),
  @('Inactive', 'Inativo'),
  @('Required', 'Obrigatório'),
  @('Optional', 'Opcional'),
  @('Invalid', 'Inválido'),
  @('Something went wrong', 'Algo deu errado'),
  @('Try again', 'Tente novamente'),
  @('Are you sure', 'Tem certeza'),
  @('are you sure', 'tem certeza'),
  @('Insert ', 'Insira '),
  @('Enter ', 'Informe '),
  @('Choose ', 'Escolha '),
  @('Click ', 'Clique '),
  @('Check it to', 'Marque para'),
  @('Copy/Past', 'Copie/Cole'),
  @('Forgot Password', 'Esqueceu a senha'),
  @('Reset Password', 'Redefinir senha'),
  @('Change Password', 'Alterar senha'),
  @('Sign up', 'Cadastre-se'),
  @('Sign in', 'Entrar'),
  @('Log in', 'Entrar'),
  @('Log out', 'Sair'),
  @('Platform Fee', 'Taxa da Plataforma'),
  @('Delivery Charge', 'Taxa de Entrega'),
  @('Subtotal', 'Subtotal'),
  @('Grand Total', 'Total Geral'),
  @('First Name', 'Nome'),
  @('Last Name', 'Sobrenome'),
  @('Zip Code', 'CEP'),
  @('Postal Code', 'CEP'),
  @('Email', 'E-mail'),
  @('Phone', 'Telefone')
)

$exact = New-Object 'System.Collections.Generic.Dictionary[string,string]' ([StringComparer]::Ordinal)
foreach ($p in $exactPairs) { if (-not $exact.ContainsKey($p[0])) { $exact[$p[0]] = $p[1] } }

$partial = New-Object 'System.Collections.Generic.List[object]'
foreach ($p in $partialPairs) { [void]$partial.Add($p) }
# Ordenar parciais por tamanho desc
$partialSorted = $partial | Sort-Object { -$_[0].Length }

function Escape-PhpSingle([string]$s) {
  return (($s -replace '\\', '\\') -replace "'", "\'")
}

function Translate-Value([string]$val) {
  if ([string]::IsNullOrEmpty($val)) { return $val }
  if ($val -match '^https?://') { return $val }
  if ($exact.ContainsKey($val)) { return $exact[$val] }

  $out = $val
  foreach ($k in @($exact.Keys)) {
    if ($out.Contains($k)) { $out = $out.Replace($k, $exact[$k]) }
  }
  foreach ($p in $partialSorted) {
    if ($out.Contains($p[0])) { $out = $out.Replace($p[0], $p[1]) }
  }
  return $out
}

function Convert-PhpLangFile([string]$src, [string]$dst) {
  $content = Get-Content -Path $src -Raw -Encoding UTF8
  $pattern = "(?s)(=>\s*)(['""])((?:\\.|(?!\2).)*)(\2)"
  $evaluator = {
    param($m)
    $prefix = $m.Groups[1].Value
    $q = $m.Groups[2].Value
    $val = $m.Groups[3].Value
    $plain = (($val -replace "\\'", "'") -replace '\\"', '"') -replace '\\\\', '\'
    $tr = Translate-Value $plain
    if ($q -eq "'") {
      $escaped = Escape-PhpSingle $tr
      return ($prefix + "'" + $escaped + "'")
    } else {
      $escaped = (($tr -replace '\\', '\\') -replace '"', '\"')
      return ($prefix + '"' + $escaped + '"')
    }
  }
  $newContent = [regex]::Replace($content, $pattern, $evaluator)
  $dir = Split-Path $dst -Parent
  if (-not (Test-Path $dir)) { New-Item -ItemType Directory -Path $dir -Force | Out-Null }
  [System.IO.File]::WriteAllText($dst, $newContent, [System.Text.UTF8Encoding]::new($false))
}

function Convert-DartLangFile([string]$src, [string]$dst, [string]$mapName) {
  $content = Get-Content -Path $src -Raw -Encoding UTF8
  $content = $content -replace 'const Map<String, String> enUS', ("const Map<String, String> " + $mapName)
  $content = $content -replace 'const Map<String, String> ptPO', ("const Map<String, String> " + $mapName)
  $pattern = "(['""])((?:\\.|(?!\1).)*)(\1)\s*:\s*(['""])((?:\\.|(?!\4).)*)(\4)"
  $evaluator = {
    param($m)
    $kq = $m.Groups[1].Value
    $key = $m.Groups[2].Value
    $vq = $m.Groups[4].Value
    $val = $m.Groups[5].Value
    $plain = ($val -replace "\\'", "'") -replace '\\"', '"'
    $tr = Translate-Value $plain
    if ($vq -eq "'") {
      $escaped = (($tr -replace '\\', '\\') -replace "'", "\'")
    } else {
      $escaped = (($tr -replace '\\', '\\') -replace '"', '\"')
    }
    return ($kq + $key + $kq + ': ' + $vq + $escaped + $vq)
  }
  $newContent = [regex]::Replace($content, $pattern, $evaluator)
  $dir = Split-Path $dst -Parent
  if (-not (Test-Path $dir)) { New-Item -ItemType Directory -Path $dir -Force | Out-Null }
  [System.IO.File]::WriteAllText($dst, $newContent, [System.Text.UTF8Encoding]::new($false))
}

$panels = @('admin', 'store', 'website')
foreach ($p in $panels) {
  $enDir = Join-Path $root ("web\" + $p + "\resources\lang\en")
  $ptDir = Join-Path $root ("web\" + $p + "\resources\lang\pt_br")
  if (-not (Test-Path $enDir)) { Write-Host ("SKIP " + $enDir); continue }
  New-Item -ItemType Directory -Path $ptDir -Force | Out-Null
  Get-ChildItem $enDir -File | ForEach-Object {
    $dst = Join-Path $ptDir $_.Name
    if ($_.Extension -eq '.php') {
      Write-Host ("Translating " + $p + "/" + $_.Name + " ...")
      Convert-PhpLangFile $_.FullName $dst
    } else {
      Copy-Item $_.FullName $dst -Force
    }
  }
}

Write-Host 'Translating customer app_pt.dart ...'
Convert-DartLangFile (Join-Path $root 'apps\customer\lib\lang\app_en.dart') (Join-Path $root 'apps\customer\lib\lang\app_pt.dart') 'ptBR'

Write-Host 'Translating store app_pt.dart ...'
Convert-DartLangFile (Join-Path $root 'apps\store\lib\lang\app_en.dart') (Join-Path $root 'apps\store\lib\lang\app_pt.dart') 'ptPO'

Write-Host 'Translating driver app_pt.dart ...'
Convert-DartLangFile (Join-Path $root 'apps\driver\lib\lang\app_en.dart') (Join-Path $root 'apps\driver\lib\lang\app_pt.dart') 'ptPO'

Write-Host 'DONE generate-pt-br'
