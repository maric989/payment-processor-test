# Payment Processor Test

This project is a payment processing system that integrates with external payment gateways like Shift4 and Aci. The project is containerized using Docker and provides API endpoints and commands for processing payments.

## Setup

1. **Copy the Environment File:**

   Copy the `.env.example` file to `.env` to set up your environment:

   ```bash
   cp .env.example .env

2. **Docker Commands:**

   To build and run the application using Docker, execute:

   ```bash
   docker-compose up --build
   ```
   If you want to run the containers in the background (detached mode):
   ```bash
   docker-compose up -d
   ```

   To stop the running containers:
   ```bash
   docker-compose down
   ```

3. **API Example Request**
   
   You can process payments by sending a POST request to the appropriate API endpoint.
   ```json
   {
   "amount": 152.45,
   "currency": "EUR",
   "card_number": "4200000000000000",
   "card_exp_year": 2026,
   "card_exp_month": 12,
   "card_cvv": "444"
   }
   ```

4. **API Endpoints**:
   ```bash
   POST /payment/shift4 - Process a payment using the Shift4 provider.
   
   POST /payment/aci - Process a payment using the Aci provider.
   ```

5. **Command Line Payment Request**
   ```bash
   php bin/console app:process-payment shift4 --amount=152.45 --currency=EUR --cardNumber=4200000000000000 --cardExpYear=2026 --cardExpMonth=12 --cardCvv=444
   ```
   
6. **Running Tests**
   
   To run unit tests using PHPUnit:
   ```bash
   docker-compose exec app ./vendor/bin/phpunit
   ```
   
   Coverage:
   ```bash
   docker-compose exec app ./vendor/bin/phpunit --coverage-html coverage/
   ```   

7. **Documentation can be found here:**
   ```bash
   http://localhost:8000/api/doc
   ```

   Ensure the Docker containers are running before executing the tests.


   