# 📱 Telas do Aplicativo eMart Customer App

## 📋 Índice de Navegação

### 🔄 Fluxo Principal do App

```
OnBoarding Screen
    ↓
Service List Screen (Dashboard de Serviços)
    ↓
[Serviços Disponíveis]
    ├─ Food Delivery (ContainerScreen)
    ├─ E-Commerce Service (EcommerceDashboard)
    ├─ Cab Service (DashboardCabService)
    ├─ Rental Service (RentalServiceDashBoard)
    ├─ Parcel Delivery (ParcelDahBoard)
    └─ On-Demand Service (OnDemandDahBoard)
```

---

## 🎯 Telas Principais por Categoria

### 1. 🔐 Autenticação e Onboarding

#### 1.1 OnBoarding Screen
- **Arquivo**: `lib/ui/onBoarding/on_boarding_screen.dart`
- **Descrição**: Tela de introdução/apresentação do app
- **Funcionalidade**: Primeira tela exibida aos novos usuários

#### 1.2 Login Screen
- **Arquivo**: `lib/ui/auth_screen/login_screen.dart`
- **Descrição**: Tela de login
- **Funcionalidades**:
  - Login com email/senha
  - Login com telefone (OTP)
  - Link para cadastro
  - Link para recuperação de senha
  - Opção de pular login (modo visitante)

#### 1.3 Phone Number Screen
- **Arquivo**: `lib/ui/auth_screen/phone_number_screen.dart`
- **Descrição**: Tela para inserir número de telefone
- **Funcionalidade**: Captura número de telefone para autenticação

#### 1.4 OTP Screen
- **Arquivo**: `lib/ui/auth_screen/otp_screen.dart`
- **Descrição**: Tela de verificação OTP
- **Funcionalidade**: Validação de código OTP enviado por SMS

#### 1.5 Signup Screen
- **Arquivo**: `lib/ui/auth_screen/signup_screen.dart`
- **Descrição**: Tela de cadastro
- **Funcionalidade**: Cadastro de novos usuários

#### 1.6 Forgot Password Screen
- **Arquivo**: `lib/ui/forgot_password_screen/forgot_password_screen.dart`
- **Descrição**: Tela de recuperação de senha
- **Funcionalidade**: Recuperação de senha via email

#### 1.7 Reauth User Screen
- **Arquivo**: `lib/ui/reauthScreen/reauth_user_screen.dart`
- **Descrição**: Tela de reautenticação
- **Funcionalidade**: Reautenticação para ações sensíveis

#### 1.8 Location Permission Screen
- **Arquivo**: `lib/ui/location_permission_screen.dart`
- **Descrição**: Tela de solicitação de permissão de localização
- **Funcionalidade**: Solicita permissão de localização e configura endereço inicial

---

### 2. 🏠 Dashboard e Navegação Principal

#### 2.1 Service List Screen
- **Arquivo**: `lib/ui/service_list_screen.dart`
- **Descrição**: Tela principal de seleção de serviços
- **Funcionalidades**:
  - Lista de todos os serviços disponíveis
  - Banners promocionais
  - Seleção de serviço (Food, E-commerce, Cab, Rental, Parcel, On-Demand)

#### 2.2 Container Screen (Food Delivery)
- **Arquivo**: `lib/ui/container/ContainerScreen.dart`
- **Descrição**: Container principal do serviço de Food Delivery
- **Funcionalidades**:
  - Drawer de navegação
  - Navegação entre telas
  - Menu lateral com opções

---

### 3. 🍕 Food Delivery Service

#### 3.1 Home Screen
- **Arquivo**: `lib/ui/home/HomeScreen.dart`
- **Descrição**: Tela inicial do Food Delivery
- **Funcionalidades**:
  - Banner de stories
  - Categorias de restaurantes
  - Restaurantes populares
  - Ofertas especiais
  - Novos restaurantes
  - Comidas próximas
  - Busca de restaurantes

#### 3.2 Vendor Products Screen
- **Arquivo**: `lib/ui/vendorProductsScreen/VendorProductsScreen.dart`
- **Descrição**: Tela de produtos do restaurante/loja
- **Funcionalidades**:
  - Menu de produtos
  - Categorias de produtos
  - Informações do restaurante
  - Avaliações
  - Fotos

#### 3.3 New Vendor Products Screen
- **Arquivo**: `lib/ui/vendorProductsScreen/newVendorProductsScreen.dart`
- **Descrição**: Nova versão da tela de produtos
- **Funcionalidades**: Similar ao VendorProductsScreen com design atualizado

#### 3.4 Product Details Screen
- **Arquivo**: `lib/ui/productDetailsScreen/ProductDetailsScreen.dart`
- **Descrição**: Detalhes do produto
- **Funcionalidades**:
  - Informações detalhadas do produto
  - Variações e opções
  - Adicionar ao carrinho
  - Avaliações do produto

#### 3.5 Cart Screen
- **Arquivo**: `lib/ui/cartScreen/CartScreen.dart`
- **Descrição**: Carrinho de compras
- **Funcionalidades**:
  - Lista de itens no carrinho
  - Aplicar cupons
  - Calcular totais
  - Prosseguir para checkout

#### 3.6 Checkout Screen
- **Arquivo**: `lib/ui/checkoutScreen/CheckoutScreen.dart`
- **Descrição**: Tela de finalização de pedido
- **Funcionalidades**:
  - Seleção de endereço de entrega
  - Seleção de método de pagamento
  - Resumo do pedido
  - Confirmar pedido

#### 3.7 Place Order Screen
- **Arquivo**: `lib/ui/placeOrderScreen/PlaceOrderScreen.dart`
- **Descrição**: Tela de confirmação de pedido
- **Funcionalidade**: Confirmação final do pedido

#### 3.8 Payment Screen
- **Arquivo**: `lib/ui/payment/PaymentScreen.dart`
- **Descrição**: Tela de pagamento
- **Funcionalidades**:
  - Múltiplos métodos de pagamento
  - Processamento de pagamento
  - Confirmação de pagamento

#### 3.9 Orders Screen
- **Arquivo**: `lib/ui/ordersScreen/OrdersScreen.dart`
- **Descrição**: Tela de histórico de pedidos
- **Funcionalidades**:
  - Lista de pedidos
  - Filtros de pedidos
  - Status dos pedidos

#### 3.10 Order Details Screen
- **Arquivo**: `lib/ui/orderDetailsScreen/OrderDetailsScreen.dart`
- **Descrição**: Detalhes do pedido
- **Funcionalidades**:
  - Informações do pedido
  - Itens do pedido
  - Status do pedido
  - Rastreamento

#### 3.11 Order Tracking Screen
- **Arquivo**: `lib/ui/orderDetailsScreen/order_tracking_screen.dart`
- **Descrição**: Rastreamento de pedido em tempo real
- **Funcionalidades**:
  - Mapa com localização
  - Status da entrega
  - Informações do entregador

#### 3.12 Review Screen
- **Arquivo**: `lib/ui/reviewScreen.dart/reviewScreen.dart`
- **Descrição**: Tela de avaliação
- **Funcionalidades**:
  - Avaliar pedido
  - Avaliar produtos
  - Avaliar restaurante
  - Comentários e fotos

#### 3.13 Review List Screen
- **Arquivo**: `lib/ui/review_list_screen/review_list_screen.dart`
- **Descrição**: Lista de avaliações
- **Funcionalidade**: Visualizar todas as avaliações

#### 3.14 Category Details Screen
- **Arquivo**: `lib/ui/categoryDetailsScreen/CategoryDetailsScreen.dart`
- **Descrição**: Detalhes da categoria
- **Funcionalidade**: Produtos por categoria

#### 3.15 Cuisines Screen
- **Arquivo**: `lib/ui/cuisinesScreen/CuisinesScreen.dart`
- **Descrição**: Tela de culinárias
- **Funcionalidade**: Filtrar por tipo de culinária

#### 3.16 Search Screen
- **Arquivo**: `lib/ui/searchScreen/SearchScreen.dart`
- **Descrição**: Tela de busca
- **Funcionalidades**:
  - Buscar restaurantes
  - Buscar produtos
  - Filtros avançados

#### 3.17 Map View Screen
- **Arquivo**: `lib/ui/mapView/MapViewScreen.dart`
- **Descrição**: Visualização de mapa
- **Funcionalidades**:
  - Mapa com restaurantes
  - Localização do usuário
  - Filtros no mapa

---

### 4. 🍽️ Dine-In Service

#### 4.1 Dine In Screen
- **Arquivo**: `lib/ui/dineInScreen/dine_in_screen.dart`
- **Descrição**: Tela de restaurantes para dine-in
- **Funcionalidade**: Lista de restaurantes com opção de reserva

#### 4.2 Dine In Restaurant Details Screen
- **Arquivo**: `lib/ui/dineInScreen/dine_in_restaurant_details_screen.dart`
- **Descrição**: Detalhes do restaurante para dine-in
- **Funcionalidades**:
  - Informações do restaurante
  - Menu
  - Reserva de mesa

#### 4.3 My Booking Screen
- **Arquivo**: `lib/ui/dineInScreen/my_booking_screen.dart`
- **Descrição**: Minhas reservas
- **Funcionalidade**: Lista de reservas feitas

#### 4.4 Up Coming Table Booking
- **Arquivo**: `lib/ui/dineInScreen/UpComingTableBooking.dart`
- **Descrição**: Próximas reservas
- **Funcionalidade**: Reservas futuras

#### 4.5 History Table Booking
- **Arquivo**: `lib/ui/dineInScreen/HistoryTableBooking.dart`
- **Descrição**: Histórico de reservas
- **Funcionalidade**: Reservas passadas

#### 4.6 Table Order Details Screen
- **Arquivo**: `lib/ui/dineInScreen/table_order_details_screen.dart`
- **Descrição**: Detalhes do pedido na mesa
- **Funcionalidades**:
  - Detalhes do pedido
  - Itens pedidos
  - Status do pedido

---

### 5. 🛒 E-Commerce Service

#### 5.1 Ecommerce Dashboard
- **Arquivo**: `lib/ecommarce_service/ecommarce_dashboard.dart`
- **Descrição**: Dashboard do E-Commerce
- **Funcionalidade**: Container principal do serviço de E-Commerce

#### 5.2 Ecommerce Home Screen
- **Arquivo**: `lib/ecommarce_service/EcommerceHomeScreen.dart`
- **Descrição**: Tela inicial do E-Commerce
- **Funcionalidades**:
  - Produtos em destaque
  - Categorias
  - Marcas
  - Ofertas

#### 5.3 View All Brand Product Screen
- **Arquivo**: `lib/ecommarce_service/view_all_brand_product_screen.dart`
- **Descrição**: Produtos por marca
- **Funcionalidade**: Lista de produtos de uma marca

#### 5.4 View All Category Product Screen
- **Arquivo**: `lib/ecommarce_service/view_all_category_product_screen.dart`
- **Descrição**: Produtos por categoria
- **Funcionalidade**: Lista de produtos de uma categoria

---

### 6. 🚕 Cab Service (Táxi/Corridas)

#### 6.1 Dashboard Cab Service
- **Arquivo**: `lib/cab_service/dashboard_cab_service.dart`
- **Descrição**: Dashboard do serviço de táxi
- **Funcionalidade**: Container principal do serviço de táxi

#### 6.2 Cab Home Screen
- **Arquivo**: `lib/cab_service/cab_home_screen.dart`
- **Descrição**: Tela inicial do serviço de táxi
- **Funcionalidades**:
  - Solicitar corrida
  - Selecionar origem e destino
  - Tipos de veículos

#### 6.3 Cab Service Screen
- **Arquivo**: `lib/cab_service/cab_service_screen.dart`
- **Descrição**: Tela de serviço de táxi
- **Funcionalidades**:
  - Mapa com localização
  - Solicitar corrida
  - Rastreamento

#### 6.4 Cab Intercity Service Screen
- **Arquivo**: `lib/cab_service/cab_intercity_service_screen.dart`
- **Descrição**: Serviço interurbano
- **Funcionalidade**: Corridas entre cidades

#### 6.5 Cab Order Screen
- **Arquivo**: `lib/cab_service/cab_order_screen.dart`
- **Descrição**: Pedidos de corrida
- **Funcionalidade**: Lista de corridas solicitadas

#### 6.6 Cab Order Detail Screen
- **Arquivo**: `lib/cab_service/cab_order_detail_screen.dart`
- **Descrição**: Detalhes da corrida
- **Funcionalidades**:
  - Informações da corrida
  - Rastreamento
  - Informações do motorista

#### 6.7 Cab Payment Selection Screen
- **Arquivo**: `lib/cab_service/cab_payment_selection_screen.dart`
- **Descrição**: Seleção de pagamento para corrida
- **Funcionalidade**: Escolher método de pagamento

#### 6.8 Cab Payment Screen
- **Arquivo**: `lib/cab_service/CabPaymentScreen.dart`
- **Descrição**: Tela de pagamento da corrida
- **Funcionalidade**: Processar pagamento

#### 6.9 Intercity Payment Selection Screen
- **Arquivo**: `lib/cab_service/intercity_payment_selection_screen.dart`
- **Descrição**: Seleção de pagamento para corrida interurbana
- **Funcionalidade**: Escolher método de pagamento

#### 6.10 Cab Review Screen
- **Arquivo**: `lib/cab_service/cab_review_screen.dart`
- **Descrição**: Avaliação da corrida
- **Funcionalidade**: Avaliar motorista e corrida

#### 6.11 Complain Screen
- **Arquivo**: `lib/cab_service/complain_screen.dart`
- **Descrição**: Reclamação
- **Funcionalidade**: Registrar reclamação sobre a corrida

---

### 7. 🚗 Rental Service (Aluguel de Veículos)

#### 7.1 Rental Service Dash Board
- **Arquivo**: `lib/rental_service/rental_service_dash_board.dart`
- **Descrição**: Dashboard do serviço de aluguel
- **Funcionalidade**: Container principal do serviço de aluguel

#### 7.2 Rental Service Home Screen
- **Arquivo**: `lib/rental_service/rental_service_home_screen.dart`
- **Descrição**: Tela inicial do serviço de aluguel
- **Funcionalidades**:
  - Lista de veículos
  - Filtros
  - Tipos de veículos

#### 7.3 Vehicle Type Screens
- **Arquivo**: `lib/rental_service/vehicle_type_screens.dart`
- **Descrição**: Tipos de veículos
- **Funcionalidade**: Selecionar tipo de veículo

#### 7.4 Vehicle Details Screen
- **Arquivo**: `lib/rental_service/vehicle_details_screen.dart`
- **Descrição**: Detalhes do veículo
- **Funcionalidades**:
  - Informações do veículo
  - Fotos
  - Especificações
  - Preços

#### 7.5 Rental Booking Screen
- **Arquivo**: `lib/rental_service/rental_booking_screen.dart`
- **Descrição**: Tela de reserva
- **Funcionalidades**:
  - Selecionar datas
  - Informações do aluguel
  - Confirmar reserva

#### 7.6 Rental Summary Screen
- **Arquivo**: `lib/rental_service/renatal_summary_screen.dart`
- **Descrição**: Resumo do aluguel
- **Funcionalidade**: Resumo antes do pagamento

#### 7.7 Rental Payment Screen
- **Arquivo**: `lib/rental_service/rental_payment_screen.dart`
- **Descrição**: Pagamento do aluguel
- **Funcionalidade**: Processar pagamento

#### 7.8 Rental Review Screen
- **Arquivo**: `lib/rental_service/rental_review_screen.dart`
- **Descrição**: Avaliação do aluguel
- **Funcionalidade**: Avaliar veículo e serviço

---

### 8. 📦 Parcel Delivery Service (Encomendas)

#### 8.1 Parcel Dashboard
- **Arquivo**: `lib/parcel_delivery/parcel_dashboard.dart`
- **Descrição**: Dashboard do serviço de encomendas
- **Funcionalidade**: Container principal do serviço de encomendas

#### 8.2 Parcel Home Screen
- **Arquivo**: `lib/parcel_delivery/parcel_ui/parcel_home_screen.dart`
- **Descrição**: Tela inicial do serviço de encomendas
- **Funcionalidades**:
  - Solicitar entrega
  - Categorias de pacotes
  - Informações

#### 8.3 Book Parcel Screen
- **Arquivo**: `lib/parcel_delivery/parcel_ui/book_parcel_screen.dart`
- **Descrição**: Reservar entrega de encomenda
- **Funcionalidades**:
  - Informações do pacote
  - Origem e destino
  - Datas

#### 8.4 Cart Parcel Screen
- **Arquivo**: `lib/parcel_delivery/parcel_ui/cart_parcel_screen.dart`
- **Descrição**: Carrinho de encomendas
- **Funcionalidade**: Revisar encomendas antes de enviar

#### 8.5 Parcel Order Detail Screen
- **Arquivo**: `lib/parcel_delivery/parcel_ui/parcel_order_detail_screen.dart`
- **Descrição**: Detalhes da encomenda
- **Funcionalidades**:
  - Informações da encomenda
  - Status
  - Rastreamento

#### 8.6 Parcel Order Track Screen
- **Arquivo**: `lib/parcel_delivery/parcel_ui/parcel_order_track_screen.dart`
- **Descrição**: Rastreamento da encomenda
- **Funcionalidades**:
  - Mapa com localização
  - Status da entrega
  - Informações do entregador

#### 8.7 Parcel Review Screen
- **Arquivo**: `lib/parcel_delivery/parcel_ui/parcel_review_screen.dart`
- **Descrição**: Avaliação do serviço de encomenda
- **Funcionalidade**: Avaliar entrega

#### 8.8 History Screen
- **Arquivo**: `lib/parcel_delivery/parcel_ui/history_screen.dart`
- **Descrição**: Histórico de encomendas
- **Funcionalidade**: Lista de encomendas enviadas

---

### 9. 🔧 On-Demand Service (Serviços Sob Demanda)

#### 9.1 On Demand Dashboard
- **Arquivo**: `lib/onDemand_service/onDemand_ui/onDemand_dashboard.dart`
- **Descrição**: Dashboard do serviço sob demanda
- **Funcionalidade**: Container principal do serviço sob demanda

#### 9.2 On Demand Home Screen
- **Arquivo**: `lib/onDemand_service/onDemand_ui/home_screen/ondemand_home_screen.dart`
- **Descrição**: Tela inicial do serviço sob demanda
- **Funcionalidades**:
  - Categorias de serviços
  - Serviços populares
  - Provedores

#### 9.3 Category Screen
- **Arquivo**: `lib/onDemand_service/onDemand_ui/category_screen/category_screen.dart`
- **Descrição**: Categorias de serviços
- **Funcionalidade**: Lista de categorias

#### 9.4 Provider Screen
- **Arquivo**: `lib/onDemand_service/onDemand_ui/provider_screen/provider_screen.dart`
- **Descrição**: Tela de provedores
- **Funcionalidade**: Lista de provedores de serviços

#### 9.5 On Demand Details Screen
- **Arquivo**: `lib/onDemand_service/onDemand_ui/ondemand_details_screen/ondemand_details_screen.dart`
- **Descrição**: Detalhes do serviço
- **Funcionalidades**:
  - Informações do serviço
  - Provedor
  - Preços
  - Agendar

#### 9.6 Booking Screen
- **Arquivo**: `lib/onDemand_service/onDemand_ui/booking_screen/booking_screen.dart`
- **Descrição**: Tela de agendamento
- **Funcionalidades**:
  - Selecionar data/hora
  - Informações do serviço
  - Confirmar agendamento

#### 9.7 On Demand Payment Screen
- **Arquivo**: `lib/onDemand_service/onDemand_ui/ondemand_payment_screen/ondemand_payment_screen.dart`
- **Descrição**: Pagamento do serviço
- **Funcionalidade**: Processar pagamento

#### 9.8 On Demand Order Screen
- **Arquivo**: `lib/onDemand_service/onDemand_ui/order_screen/ondemand_order_screen.dart`
- **Descrição**: Pedidos de serviços
- **Funcionalidade**: Lista de serviços agendados

#### 9.9 On Demand Order Details Screen
- **Arquivo**: `lib/onDemand_service/onDemand_ui/order_screen/ondemand_order_details_screen.dart`
- **Descrição**: Detalhes do pedido
- **Funcionalidades**:
  - Informações do pedido
  - Status
  - Profissional

#### 9.10 On Demand Review Screen
- **Arquivo**: `lib/onDemand_service/onDemand_ui/review_screen/ondemand_review_screen.dart`
- **Descrição**: Avaliação do serviço
- **Funcionalidade**: Avaliar serviço e profissional

#### 9.11 On Demand Favorite Screen
- **Arquivo**: `lib/onDemand_service/onDemand_ui/favorite_screen/ondemand_favorite_screen.dart`
- **Descrição**: Serviços favoritos
- **Funcionalidade**: Lista de serviços favoritados

#### 9.12 View All Popular Service Screen
- **Arquivo**: `lib/onDemand_service/onDemand_ui/provider_service_screen/view_all_popular_service_screen.dart`
- **Descrição**: Serviços populares
- **Funcionalidade**: Lista de serviços populares

#### 9.13 View Category Service List Screen
- **Arquivo**: `lib/onDemand_service/onDemand_ui/provider_service_screen/view_category_service_list_screen.dart`
- **Descrição**: Serviços por categoria
- **Funcionalidade**: Lista de serviços de uma categoria

---

### 10. 👤 Perfil e Configurações

#### 10.1 Profile Screen
- **Arquivo**: `lib/ui/profile/ProfileScreen.dart`
- **Descrição**: Tela de perfil do usuário
- **Funcionalidades**:
  - Informações do perfil
  - Editar perfil
  - Foto de perfil
  - Configurações

#### 10.2 Account Details Screen
- **Arquivo**: `lib/ui/accountDetails/AccountDetailsScreen.dart`
- **Descrição**: Detalhes da conta
- **Funcionalidades**:
  - Editar informações
  - Alterar senha
  - Configurações de conta

#### 10.3 Settings Screen
- **Arquivo**: `lib/ui/settings/SettingsScreen.dart`
- **Descrição**: Configurações do app
- **Funcionalidades**:
  - Preferências
  - Notificações
  - Idioma
  - Tema

---

### 11. 📍 Localização e Endereços

#### 11.1 Delivery Address Screen
- **Arquivo**: `lib/ui/deliveryAddressScreen/DeliveryAddressScreen.dart`
- **Descrição**: Endereços de entrega
- **Funcionalidades**:
  - Lista de endereços
  - Adicionar endereço
  - Editar endereço
  - Endereço padrão

#### 11.2 Add Address Screen
- **Arquivo**: `lib/ui/deliveryAddressScreen/add_address_screen.dart`
- **Descrição**: Adicionar endereço
- **Funcionalidades**:
  - Formulário de endereço
  - Seleção no mapa
  - Salvar endereço

#### 11.3 Current Address Change Screen
- **Arquivo**: `lib/ui/home/CurrentAddressChangeScreen.dart`
- **Descrição**: Alterar endereço atual
- **Funcionalidade**: Alterar endereço de entrega atual

---

### 12. 💬 Chat e Mensagens

#### 12.1 Inbox Screen
- **Arquivo**: `lib/ui/chat_screen/inbox_screen.dart`
- **Descrição**: Caixa de entrada
- **Funcionalidade**: Lista de conversas

#### 12.2 Chat Screen
- **Arquivo**: `lib/ui/chat_screen/chat_screen.dart`
- **Descrição**: Tela de chat
- **Funcionalidades**:
  - Mensagens
  - Envio de imagens
  - Envio de vídeos

#### 12.3 Inbox Driver Screen
- **Arquivo**: `lib/ui/chat_screen/inbox_driver_screen.dart`
- **Descrição**: Chat com motorista
- **Funcionalidade**: Conversas com motoristas

#### 12.4 Inbox Provider Screen
- **Arquivo**: `lib/ui/chat_screen/inbox_provider_screen.dart`
- **Descrição**: Chat com provedor
- **Funcionalidade**: Conversas com provedores de serviços

#### 12.5 Inbox Worker Screen
- **Arquivo**: `lib/ui/chat_screen/inbox_worker_screen.dart`
- **Descrição**: Chat com trabalhador
- **Funcionalidade**: Conversas com trabalhadores

#### 12.6 Chat Screen (Alternativa)
- **Arquivo**: `lib/ui/chat/ChatScreen.dart`
- **Descrição**: Tela de chat alternativa
- **Funcionalidade**: Chat em tempo real

---

### 13. 💰 Carteira e Pagamentos

#### 13.1 Wallet Screen
- **Arquivo**: `lib/ui/wallet/walletScreen.dart`
- **Descrição**: Carteira digital
- **Funcionalidades**:
  - Saldo da carteira
  - Recarga
  - Histórico de transações
  - Pagamento com carteira

#### 13.2 PayStack Screen
- **Arquivo**: `lib/ui/wallet/payStackScreen.dart`
- **Descrição**: Pagamento via PayStack
- **Funcionalidade**: Processar pagamento PayStack

#### 13.3 PayFast Screen
- **Arquivo**: `lib/ui/wallet/PayFastScreen.dart`
- **Descrição**: Pagamento via PayFast
- **Funcionalidade**: Processar pagamento PayFast

#### 13.4 MercadoPago Screen
- **Arquivo**: `lib/ui/wallet/MercadoPagoScreen.dart`
- **Descrição**: Pagamento via MercadoPago
- **Funcionalidade**: Processar pagamento MercadoPago

---

### 14. 🎁 Gift Cards

#### 14.1 Gift Card Screen
- **Arquivo**: `lib/ui/gift_card/gift_card_screen.dart`
- **Descrição**: Tela de gift cards
- **Funcionalidades**:
  - Lista de gift cards
  - Comprar gift card
  - Resgatar gift card

#### 14.2 Gift Card Purchase Screen
- **Arquivo**: `lib/ui/gift_card/gift_card_purchase_screen.dart`
- **Descrição**: Compra de gift card
- **Funcionalidade**: Comprar gift card

#### 14.3 Gift Card Redeem Screen
- **Arquivo**: `lib/ui/gift_card/gift_card_redeem_screen.dart`
- **Descrição**: Resgate de gift card
- **Funcionalidade**: Resgatar gift card

#### 14.4 Gift Card History List Screen
- **Arquivo**: `lib/ui/gift_card/gift_card_history_list_screen.dart`
- **Descrição**: Histórico de gift cards
- **Funcionalidade**: Lista de gift cards comprados/resgatados

---

### 15. ⭐ Favoritos

#### 15.1 Favourite Store
- **Arquivo**: `lib/ui/home/favourite_store.dart`
- **Descrição**: Lojas favoritas
- **Funcionalidade**: Lista de lojas favoritadas

#### 15.2 Favourite Item
- **Arquivo**: `lib/ui/home/favourite_item.dart`
- **Descrição**: Itens favoritos
- **Funcionalidade**: Lista de itens favoritados

---

### 16. 🔍 Visualizações e Utilitários

#### 16.1 Full Screen Image Viewer
- **Arquivo**: `lib/ui/fullScreenImageViewer/FullScreenImageViewer.dart`
- **Descrição**: Visualizador de imagem em tela cheia
- **Funcionalidade**: Visualizar imagens em tela cheia

#### 16.2 Full Screen Video Viewer
- **Arquivo**: `lib/ui/fullScreenVideoViewer/FullScreenVideoViewer.dart`
- **Descrição**: Visualizador de vídeo em tela cheia
- **Funcionalidade**: Visualizar vídeos em tela cheia

#### 16.3 Story View
- **Arquivo**: `lib/ui/home/story_view.dart`
- **Descrição**: Visualização de stories
- **Funcionalidade**: Ver stories de lojas/restaurantes

#### 16.4 QR Code Scanner
- **Arquivo**: `lib/ui/QrCodeScanner/QrCodeScanner.dart`
- **Descrição**: Scanner de QR Code
- **Funcionalidade**: Escanear códigos QR

---

### 17. 📱 Telas de Visualização (View All)

#### 17.1 View All New Arrival Store Screen
- **Arquivo**: `lib/ui/home/view_all_new_arrival_store_screen.dart`
- **Descrição**: Todos os novos restaurantes
- **Funcionalidade**: Lista completa de novos restaurantes

#### 17.2 View All Offer Screen
- **Arquivo**: `lib/ui/home/view_all_offer_screen.dart`
- **Descrição**: Todas as ofertas
- **Funcionalidade**: Lista completa de ofertas

#### 17.3 View All Popular Food Near By Screen
- **Arquivo**: `lib/ui/home/view_all_popular_food_near_by_screen.dart`
- **Descrição**: Todas as comidas populares próximas
- **Funcionalidade**: Lista completa de comidas populares

#### 17.4 View All Popular Store Screen
- **Arquivo**: `lib/ui/home/view_all_popular_store_screen.dart`
- **Descrição**: Todas as lojas populares
- **Funcionalidade**: Lista completa de lojas populares

#### 17.5 View All Restaurant
- **Arquivo**: `lib/ui/home/view_all_restaurant.dart`
- **Descrição**: Todos os restaurantes
- **Funcionalidade**: Lista completa de restaurantes

---

### 18. 📞 Suporte e Informações

#### 18.1 Contact Us Screen
- **Arquivo**: `lib/ui/contactUs/ContactUsScreen.dart`
- **Descrição**: Fale conosco
- **Funcionalidades**:
  - Formulário de contato
  - Informações de contato
  - Enviar mensagem

#### 18.2 Privacy Policy
- **Arquivo**: `lib/ui/privacy_policy/privacy_policy.dart`
- **Descrição**: Política de privacidade
- **Funcionalidade**: Visualizar política de privacidade

#### 18.3 Terms and Condition
- **Arquivo**: `lib/ui/termsAndCondition/terms_and_codition.dart`
- **Descrição**: Termos e condições
- **Funcionalidade**: Visualizar termos e condições

---

### 19. 🌐 Configurações de Idioma

#### 19.1 Language Choose Screen
- **Arquivo**: `lib/ui/Language/language_choose_screen.dart`
- **Descrição**: Seleção de idioma
- **Funcionalidades**:
  - Lista de idiomas
  - Selecionar idioma
  - Aplicar idioma

---

### 20. 👥 Referências

#### 20.1 Referral Screen
- **Arquivo**: `lib/ui/referral_screen/referral_screen.dart`
- **Descrição**: Sistema de referências
- **Funcionalidades**:
  - Código de referência
  - Compartilhar código
  - Histórico de referências

---

### 21. 🛠️ Componentes e Sheets

#### 21.1 Cart Options Sheet
- **Arquivo**: `lib/ui/cartOptionsSheet/CartOptionsSheet.dart`
- **Descrição**: Opções do carrinho
- **Funcionalidade**: Menu de opções do carrinho

---

## 📊 Estatísticas das Telas

- **Total de Telas**: ~100+ telas
- **Telas de Autenticação**: 7
- **Telas de Serviços**: 60+
- **Telas de Perfil/Configurações**: 5
- **Telas de Pagamento**: 10+
- **Telas de Chat**: 6
- **Telas de Utilitários**: 10+

## 🎨 Características das Telas

### Design
- ✅ Material Design
- ✅ Suporte a tema claro/escuro
- ✅ Responsivo
- ✅ Animações suaves
- ✅ Ícones SVG e imagens

### Funcionalidades Comuns
- ✅ Navegação intuitiva
- ✅ Busca e filtros
- ✅ Carregamento assíncrono
- ✅ Tratamento de erros
- ✅ Notificações
- ✅ Multilíngue (6 idiomas)

### Integrações
- ✅ Firebase (Auth, Firestore, Storage)
- ✅ Google Maps
- ✅ Pagamentos múltiplos
- ✅ Chat em tempo real
- ✅ Notificações push
- ✅ Câmera e galeria

## 🚀 Navegação Principal

### Drawer Menu (Menu Lateral)
- Dashboard
- Home
- Wallet
- Dine-In
- Cuisines
- Search
- Cart
- Referral
- Profile
- Orders
- My Booking
- Language
- Inbox
- Driver
- Terms & Conditions
- Privacy Policy
- Liked Store
- Liked Product
- Gift Card
- Logout

### Bottom Navigation
- Home
- Search
- Orders
- Profile
- Cart (badge com contador)

---

## 📝 Notas Importantes

1. **Múltiplos Serviços**: O app suporta 6 tipos diferentes de serviços, cada um com suas próprias telas
2. **Navegação Complexa**: A navegação varia dependendo do serviço selecionado
3. **Tema Personalizável**: Cores e temas podem ser personalizados por serviço
4. **Multilíngue**: Suporte a 6 idiomas (Inglês, Árabe, Holandês, Francês, Italiano, Russo)
5. **Modo Offline**: Algumas funcionalidades funcionam offline
6. **Real-time**: Atualizações em tempo real via Firebase

---

**Última atualização**: Dezembro 2024
**Total de Telas Documentadas**: 100+

