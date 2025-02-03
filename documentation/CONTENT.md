# Site Content Types

## Domain/Portfolio Content Types

```mermaid
classDiagram
    class Sector {
        string locale
        project[] projects
        service[] services
        person[] people

        post[] related
    }

    class Service {
        string locale
        project[] projects
        sector[] sectors
        person[] people

        post[] related
    }

    class Office {
        string location

        multi office_details
        string name
        string email
        string phone
        string address

        project[] projects
        sector[] sectors
        service[] services
        person[] people

        post[] related
    }

    class Project {
        string client
        string facility
        string location
        date start_date
        date completion_date
        string owner
        string architect
        string vendors
        string contractors

        key_val[] statistics

        enum visibility
        attachment case_study

        sector[] sectors
        service[] services
        person[] people

        post[] related
    }

    class Person {
        string role
        email email
        string phone
        project[] projects
        sector[] sectors
        service[] services
        office[] offices

        post[] related
    }
```

## News/Thought Leadership content types

```mermaid
classDiagram
    class Event {
        datetime start_date
        datetime end_date
        boolean all_day
        url event_url
        email meeting_email

        string venue_name
        string venue_url
        string venue_phone
        string venue_address
        string venue_city
        string venue_state
        string venue_postcode
        string venue_country

        person[] people
        portfolio_post[] related
    }

    class Post {
        person[] people
        portfolio_post[] related
        region[] regions
        json podcast_info
        json reprint_info
    }

    class Page {
        string locale
    }
```

## Relational Overview

The core cluster of business domain post types are used to build the Cumming Group portfolio.

A bidirectional `related` relationship is also used to connect these business domain post types to related news articles, and vice-versa. The core business domain post types may only point to `post`, `event`, and `episode` content in that field, and the news/thought leadership posts may only relate to `project`, `sector`, `service`, `region`, `office`, or `person` posts.

```mermaid
erDiagram
    PROJECT }o--o{ SECTOR : served
    PROJECT }o--o{ REGION : is-in
    PROJECT }o--o{ SERVICE : utilized

    PROJECT }o--|| OFFICE : was-staffed-by
    REGION }o--|| OFFICE : is-served-by
    REGION }o--o| REGION : is-in

    SERVICE }o--o{ PERSON : has-leader
    OFFICE |o--o{ PERSON : has-contact
    REGION }o--o{ PERSON : features
    SECTOR }o--o{ PERSON : has-leader
```
