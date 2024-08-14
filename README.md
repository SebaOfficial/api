# Seba's API
This is a collection of useful APIs for Sebastiano's projects.

## Endpoints

### Payments
| HTTP Method   | Endpoint                   | Authentication | Description                                                                                       |
|---------------|----------------------------|----------------|---------------------------------------------------------------------------------------------------|
| `GET`         | `/pay`                     | None           | Get the list of available payment methods.                                                        |
| `GET`\|`POST` | `/pay/:method/:amount`     | None           | Create a new payment link. The amount is in euro cents. Use `GET` to be automatically redirected. |

### Newsletter
| HTTP Method | Endpoint                   | Authentication | Description                                                                 |
|-------------|----------------------------|----------------|-----------------------------------------------------------------------------|
| `GET`       | `/newsletter.js`           | None           | Get the script that injects the newsletter prompt into the DOM.             |
| `POST`      | `/newsletter/sub/`         | None           | Create a new subscriber. Pass the `email` in the body of the request.       |
| `DELETE`    | `/newsletter/sub/`         | Bearer         | Delete a subscriber. Pass `auth_token` in as a Bearer Authorization header. |
| `GET`       | `/newsletter/unsub/:token` | None           | Alias for `DELETE /newsletter/sub/`.                                        |
| `POST`      | `/newsletter/post/`        | Bearer         | Create a new post to send to all subscribers.                               |

