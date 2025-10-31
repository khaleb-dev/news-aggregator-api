## Built with Laravel

A take-home challenge for the Backend Web Developer position. The challenge is to build the backend functionality for
a news aggregator website that pulls articles from various sources and serves them to the
frontend application.

## Task Requirement

- Data aggregation and storage: Implement a backend system that fetches articles from selected data sources
(choose at least 3 from the provided list) and stores them locally in a database. Ensure that the data is regularly
updated from the live data sources.
- API endpoints: Create API endpoints for the frontend application to interact with the backend. These endpoints
should allow the frontend to retrieve articles based on search queries, filtering criteria (date, category, source), and
user preferences (selected sources, categories, authors).


## Datasources Used:

- [NewsAPI.ai](https://newsapi.ai)
- [News API](https://newsapi.org)
- [The Guardian](https://open-platform.theguardian.com)

To run this project, you will need api keys from each of this platforms.

## How To Use

1. First, get api keys from NewsAPI.ai, News API and The Guardian.
2. Set the api key in the corresponding variable in the .env file.
3. Run ```php artisan migrate:fresh``` to migrate database schema.
4. Run ```php artisan serve``` to start the project.
5. Open http://127.0.0.1:8000/docs/api to view its api documentation.
6. I used Laravel's schedule to perform the background sync. You can try it using this command: ```php artisan schedule:run```

