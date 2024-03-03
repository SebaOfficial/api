# Seba's API
This is a collection of useful APIs for Sebastiano's projects.

## Endpoints

### Payments
| HTTP Method   | Endpoint               | Authentication | Description                                             |
|---------------|------------------------|----------------|---------------------------------------------------------|
| `GET`         | `/pay`                 | None           | Get the list of available payment methods.              |
| `GET`\|`POST` | `/pay/:method/:amount` | None           | Create a new payment link. The amount is in euro cents. |
