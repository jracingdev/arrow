# 🗺️ Estrutura de Navegação do App eMart

## 📱 Fluxo Principal de Navegação

```
┌─────────────────────────────────────────────────────────────┐
│                    ONBOARDING SCREEN                        │
│              (Primeira vez - Apresentação)                  │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│                  SERVICE LIST SCREEN                        │
│           (Dashboard Principal - Seleção de Serviços)       │
│                                                             │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐  │
│  │  Food    │  │E-Commerce│  │   Cab    │  │  Rental  │  │
│  │ Delivery │  │  Service │  │ Service  │  │ Service  │  │
│  └────┬─────┘  └────┬─────┘  └────┬─────┘  └────┬─────┘  │
│       │             │             │             │          │
│  ┌────┴─────┐  ┌────┴─────┐  ┌────┴─────┐  ┌────┴─────┐  │
│  │ Parcel   │  │ On-Demand│  │          │  │          │  │
│  │ Delivery │  │ Service  │  │          │  │          │  │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘  │
└─────────────────────────────────────────────────────────────┘
```

## 🍕 Fluxo: Food Delivery Service

```
ContainerScreen (Dashboard)
    │
    ├─▶ HomeScreen
    │   ├─▶ VendorProductsScreen (Menu do Restaurante)
    │   │   ├─▶ ProductDetailsScreen
    │   │   └─▶ CartScreen
    │   ├─▶ CategoryDetailsScreen
    │   ├─▶ SearchScreen
    │   ├─▶ MapViewScreen
    │   └─▶ CuisinesScreen
    │
    ├─▶ CartScreen
    │   └─▶ CheckoutScreen
    │       └─▶ PlaceOrderScreen
    │           └─▶ PaymentScreen
    │
    ├─▶ OrdersScreen
    │   └─▶ OrderDetailsScreen
    │       └─▶ OrderTrackingScreen
    │
    ├─▶ ProfileScreen
    │   └─▶ AccountDetailsScreen
    │
    └─▶ [Drawer Menu]
        ├─▶ WalletScreen
        ├─▶ DineInScreen
        ├─▶ FavouriteStore
        ├─▶ FavouriteItem
        ├─▶ ReferralScreen
        ├─▶ GiftCardScreen
        ├─▶ InboxScreen
        ├─▶ SettingsScreen
        └─▶ [Outros]
```

## 🛒 Fluxo: E-Commerce Service

```
EcommerceDashboard
    │
    ├─▶ EcommerceHomeScreen
    │   ├─▶ ViewAllBrandProductScreen
    │   ├─▶ ViewAllCategoryProductScreen
    │   ├─▶ ProductDetailsScreen
    │   └─▶ CartScreen
    │
    ├─▶ CartScreen
    │   └─▶ CheckoutScreen
    │
    ├─▶ OrdersScreen
    │   └─▶ OrderDetailsScreen
    │
    └─▶ [Drawer Menu - Similar ao Food Delivery]
```

## 🚕 Fluxo: Cab Service (Táxi)

```
DashboardCabService
    │
    ├─▶ CabHomeScreen
    │   ├─▶ CabServiceScreen (Solicitar Corrida)
    │   │   └─▶ CabPaymentSelectionScreen
    │   │       └─▶ CabPaymentScreen
    │   │
    │   └─▶ CabIntercityServiceScreen (Corrida Interurbana)
    │       └─▶ IntercityPaymentSelectionScreen
    │
    ├─▶ CabOrderScreen (Histórico de Corridas)
    │   └─▶ CabOrderDetailScreen
    │       └─▶ CabReviewScreen
    │
    └─▶ [Drawer Menu]
        ├─▶ InboxDriverScreen (Chat com Motorista)
        └─▶ ComplainScreen (Reclamações)
```

## 🚗 Fluxo: Rental Service (Aluguel)

```
RentalServiceDashBoard
    │
    ├─▶ RentalServiceHomeScreen
    │   ├─▶ VehicleTypeScreens
    │   │   └─▶ VehicleDetailsScreen
    │   │       └─▶ RentalBookingScreen
    │   │           └─▶ RentalSummaryScreen
    │   │               └─▶ RentalPaymentScreen
    │   │
    │   └─▶ RentalReviewScreen
    │
    └─▶ [Drawer Menu]
```

## 📦 Fluxo: Parcel Delivery Service

```
ParcelDahBoard
    │
    ├─▶ ParcelHomeScreen
    │   └─▶ BookParcelScreen
    │       └─▶ CartParcelScreen
    │           └─▶ PaymentScreen
    │
    ├─▶ HistoryScreen (Histórico)
    │   └─▶ ParcelOrderDetailScreen
    │       └─▶ ParcelOrderTrackScreen
    │           └─▶ ParcelReviewScreen
    │
    └─▶ [Drawer Menu]
```

## 🔧 Fluxo: On-Demand Service

```
OnDemandDahBoard
    │
    ├─▶ OnDemandHomeScreen
    │   ├─▶ CategoryScreen
    │   │   └─▶ ViewCategoryServiceListScreen
    │   │
    │   ├─▶ ProviderScreen
    │   │   └─▶ OnDemandDetailsScreen
    │   │       └─▶ BookingScreen
    │   │           └─▶ OnDemandPaymentScreen
    │   │
    │   └─▶ ViewAllPopularServiceScreen
    │
    ├─▶ OnDemandOrderScreen
    │   └─▶ OnDemandOrderDetailsScreen
    │       └─▶ OnDemandReviewScreen
    │
    ├─▶ OnDemandFavoriteScreen
    │
    └─▶ [Drawer Menu]
        └─▶ InboxProviderScreen (Chat com Provedor)
```

## 🔐 Fluxo: Autenticação

```
OnBoardingScreen
    │
    ├─▶ LoginScreen
    │   ├─▶ PhoneNumberScreen
    │   │   └─▶ OTPScreen
    │   │
    │   ├─▶ SignupScreen
    │   │
    │   └─▶ ForgotPasswordScreen
    │
    └─▶ LocationPermissionScreen
        └─▶ ServiceListScreen
```

## 💰 Fluxo: Pagamentos

```
PaymentScreen (Geral)
    │
    ├─▶ Stripe Payment
    ├─▶ PayPal Payment
    ├─▶ Razorpay Payment
    ├─▶ PayStack Payment (WalletScreen → PayStackScreen)
    ├─▶ PayFast Payment (WalletScreen → PayFastScreen)
    ├─▶ MercadoPago Payment (WalletScreen → MercadoPagoScreen)
    ├─▶ COD (Cash on Delivery)
    └─▶ Wallet Payment
```

## 💬 Fluxo: Chat

```
InboxScreen (Caixa de Entrada)
    │
    ├─▶ ChatScreen (Chat Geral)
    ├─▶ InboxDriverScreen (Chat com Motorista)
    ├─▶ InboxProviderScreen (Chat com Provedor)
    └─▶ InboxWorkerScreen (Chat com Trabalhador)
```

## 📍 Fluxo: Endereços

```
DeliveryAddressScreen
    │
    ├─▶ AddAddressScreen
    │   └─▶ MapViewScreen (Seleção no Mapa)
    │
    └─▶ CurrentAddressChangeScreen
```

## 🎁 Fluxo: Gift Cards

```
GiftCardScreen
    │
    ├─▶ GiftCardPurchaseScreen
    │   └─▶ PaymentScreen
    │
    ├─▶ GiftCardRedeemScreen
    │
    └─▶ GiftCardHistoryListScreen
```

## 🍽️ Fluxo: Dine-In

```
DineInScreen
    │
    ├─▶ DineInRestaurantDetailsScreen
    │   └─▶ TableOrderDetailsScreen
    │
    ├─▶ MyBookingScreen
    │   ├─▶ UpComingTableBooking
    │   └─▶ HistoryTableBooking
    │
    └─▶ TableOrderDetailsScreen
```

## 👤 Fluxo: Perfil

```
ProfileScreen
    │
    ├─▶ AccountDetailsScreen
    │   └─▶ ReauthUserScreen (Para alterações sensíveis)
    │
    ├─▶ SettingsScreen
    │   └─▶ LanguageChooseScreen
    │
    ├─▶ DeliveryAddressScreen
    │
    ├─▶ WalletScreen
    │
    ├─▶ ReferralScreen
    │
    ├─▶ FavouriteStore
    │
    ├─▶ FavouriteItem
    │
    └─▶ [Outros]
```

## 🔍 Fluxo: Busca e Visualização

```
SearchScreen
    │
    ├─▶ VendorProductsScreen
    ├─▶ ProductDetailsScreen
    └─▶ CategoryDetailsScreen

MapViewScreen
    │
    └─▶ VendorProductsScreen (Ao clicar no marcador)

FullScreenImageViewer
FullScreenVideoViewer
StoryView
QRCodeScanner
```

## 📞 Fluxo: Suporte

```
ContactUsScreen
PrivacyPolicy
TermsAndCondition
```

## 🎯 Navegação por Drawer Menu

Cada serviço tem seu próprio drawer menu com as seguintes opções:

### Opções Comuns:
- ✅ Dashboard (Volta para ServiceListScreen)
- ✅ Home (Tela inicial do serviço)
- ✅ Wallet (Carteira digital)
- ✅ Profile (Perfil do usuário)
- ✅ Orders (Pedidos/Histórico)
- ✅ Language (Seleção de idioma)
- ✅ Terms & Conditions
- ✅ Privacy Policy
- ✅ Gift Card
- ✅ Referral (Sistema de referências)
- ✅ Logout

### Opções Específicas por Serviço:

**Food Delivery:**
- Dine-In
- Cuisines
- Search
- Cart
- My Booking
- Inbox
- Liked Store
- Liked Product

**Cab Service:**
- Driver (Chat com motorista)

**On-Demand:**
- Inbox Provider (Chat com provedor)

## 📊 Estatísticas de Navegação

- **Telas Principais**: 6 (um para cada serviço)
- **Telas de Autenticação**: 7
- **Telas de Serviços**: 60+
- **Telas de Perfil**: 5
- **Telas de Pagamento**: 10+
- **Telas de Chat**: 6
- **Total de Telas**: 100+

## 🔄 Navegação entre Serviços

```
ServiceListScreen
    │
    ├─▶ ContainerScreen (Food Delivery)
    ├─▶ EcommerceDashboard (E-Commerce)
    ├─▶ DashboardCabService (Cab)
    ├─▶ RentalServiceDashBoard (Rental)
    ├─▶ ParcelDahBoard (Parcel)
    └─▶ OnDemandDahBoard (On-Demand)
```

**Nota**: Ao trocar de serviço, o carrinho é limpo (com confirmação do usuário).

## 🎨 Características de Navegação

- ✅ Navegação intuitiva
- ✅ Breadcrumbs visuais
- ✅ Botão de voltar funcional
- ✅ Double-tap para sair (nas telas principais)
- ✅ Drawer menu em todas as telas principais
- ✅ Bottom navigation (em algumas telas)
- ✅ Deep linking suportado
- ✅ Navegação por gestos

---

**Última atualização**: Dezembro 2024

