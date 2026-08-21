# Arrow Prestador (On Demand)

App Flutter nativo, equivalente ao painel `store.arrow.app.br` (`role=provider`, rotas `/provider/bookings`, `/provider/services`, `/provider/workers`).

Pacote Android: `br.app.arrow.provider`.  
Firebase App ID: `1:661081769489:android:e3046689b04067d2a4d3b0` (projeto **j-arrow**).

## Telas

- **Login** — e-mail/senha, telefone OTP e Google. Só `users.role == provider` e `active`.
- **Pedidos** — `provider_orders` onde `provider.author` = uid. Abas iguais à loja: novos / em andamento / concluídos / cancelados.
- **Detalhe** — Aceitar (`Order Accepted`) / Recusar (`Order Rejected`) / Atribuir (`Order Assigned` + `workerId`) / Iniciar (`Order Ongoing` + `startTime`) / Concluir com OTP (`Order Completed` + `endTime`).
- **Serviços** — `providers_services` (`author`).
- **Equipe** — `providers_workers` (`providerId`).
- **Perfil** + **Carteira** — `users.wallet_amount`.

## Assinatura

Usa `apps/keystore/key.properties` (não versionado), igual aos outros apps.
