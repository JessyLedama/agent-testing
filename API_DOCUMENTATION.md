# Music Streaming Platform API Documentation

This is a simple music streaming platform built with Laravel. Users can create accounts and stream available music. Users can become artists by clicking a button and then upload music.

## Features

- User authentication with Laravel Sanctum
- Users can become artists
- Artists can upload music (draft or published)
- Music belongs to a category
- Music has a status (draft/published) managed in its own model
- Published music is available to everyone
- Draft music is only visible to the owner
- New music is automatically added to the artist's playlist

## Database Structure

### Models

1. **User** - Manages user accounts
   - `id`
   - `name`
   - `email`
   - `password`
   - `is_artist` (boolean)
   
2. **Status** - Manages music status (draft/published)
   - `id`
   - `name` (unique)

3. **Category** - Manages music categories
   - `id`
   - `name`
   - `description`

4. **Music** - Manages music tracks
   - `id`
   - `title`
   - `artist_id` (foreign key to users)
   - `category_id` (foreign key to categories)
   - `status_id` (foreign key to statuses)
   - `file_path`
   - `duration` (in seconds)

5. **Artist Playlist** (Pivot table) - Manages artist playlists
   - `user_id`
   - `music_id`

## API Endpoints

### Authentication

Use Laravel Sanctum for authentication. Include the token in the `Authorization` header as `Bearer {token}`.

### User Endpoints

#### Become an Artist
```
POST /api/user/become-artist
```
**Auth Required:** Yes  
**Description:** Converts the authenticated user to an artist.

**Response:**
```json
{
  "message": "You are now an artist!",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "is_artist": true
  }
}
```

### Category Endpoints

#### List All Categories
```
GET /api/categories
```
**Auth Required:** No  
**Description:** Returns all music categories.

#### Get Single Category
```
GET /api/categories/{id}
```
**Auth Required:** No  
**Description:** Returns a specific category with its music.

#### Create Category
```
POST /api/categories
```
**Auth Required:** No  
**Body:**
```json
{
  "name": "Country",
  "description": "Country music"
}
```

#### Update Category
```
PUT /api/categories/{id}
```
**Auth Required:** No  
**Body:**
```json
{
  "name": "Updated Name",
  "description": "Updated description"
}
```

#### Delete Category
```
DELETE /api/categories/{id}
```
**Auth Required:** No

### Music Endpoints

#### List Music
```
GET /api/music
```
**Auth Required:** No (but optional for viewing own music)  
**Query Parameters:**
- `my_music=1` - When authenticated, returns all music owned by the authenticated user (including drafts)

**Description:** 
- Public access: Returns only published music
- With `my_music=1` and authentication: Returns all music owned by the authenticated user

**Response:**
```json
[
  {
    "id": 1,
    "title": "Summer Vibes",
    "artist_id": 2,
    "category_id": 1,
    "status_id": 2,
    "file_path": "music/summer-vibes.mp3",
    "duration": 210,
    "artist": {
      "id": 2,
      "name": "John Artist"
    },
    "category": {
      "id": 1,
      "name": "Pop"
    },
    "status": {
      "id": 2,
      "name": "published"
    }
  }
]
```

#### Get Single Music Track
```
GET /api/music/{id}
```
**Auth Required:** No (for published), Yes (for drafts - must be owner)  
**Description:** Returns a specific music track. Authorization checks:
- Published music: accessible to everyone
- Draft music: only accessible to the owner

#### Upload Music
```
POST /api/music
```
**Auth Required:** Yes (must be an artist)  
**Content-Type:** multipart/form-data  
**Body:**
```
title: "My New Song"
category_id: 1
status_id: 1
file: [audio file - mp3, wav, ogg, flac] (max 20MB)
duration: 180 (optional, in seconds)
```

**Description:** Uploads a new music track. The music is automatically added to the artist's playlist.

**Response:**
```json
{
  "message": "Music uploaded successfully",
  "music": {
    "id": 6,
    "title": "My New Song",
    "artist_id": 2,
    "category_id": 1,
    "status_id": 1,
    "file_path": "music/xyz.mp3",
    "duration": 180
  }
}
```

#### Update Music
```
PUT /api/music/{id}
```
**Auth Required:** Yes (must be the owner)  
**Body:**
```json
{
  "title": "Updated Title",
  "category_id": 2,
  "status_id": 2,
  "duration": 200
}
```

**Description:** Updates a music track. Only the owner can update their music.

#### Delete Music
```
DELETE /api/music/{id}
```
**Auth Required:** Yes (must be the owner)  
**Description:** Deletes a music track and its associated file. Only the owner can delete their music.

## Setup Instructions

1. Clone the repository
2. Install dependencies:
   ```bash
   composer install
   ```

3. Copy `.env.example` to `.env` and configure your database:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Run migrations and seeders:
   ```bash
   php artisan migrate:fresh --seed
   ```

5. Run tests:
   ```bash
   php artisan test
   ```

## Example Data

After running the seeder, you'll have:

**Users:**
- Regular User: `user@example.com`
- Artist 1: `artist1@example.com` (John Artist)
- Artist 2: `artist2@example.com` (Jane Musician)

**Categories:**
- Pop
- Rock
- Hip Hop
- Classical
- Jazz
- Electronic

**Statuses:**
- draft
- published

**Sample Music:**
- "Summer Vibes" by John Artist (Pop, Published)
- "Rock Your World" by John Artist (Rock, Published)
- "Work in Progress" by John Artist (Pop, Draft - only visible to John)
- "Smooth Jazz Night" by Jane Musician (Jazz, Published)
- "Midnight Blues" by Jane Musician (Jazz, Published)

## Authorization Rules

1. **Becoming an Artist:** Any authenticated user can become an artist
2. **Uploading Music:** Only artists can upload music
3. **Viewing Music:** 
   - Published music: Everyone (including guests)
   - Draft music: Only the owner
4. **Updating Music:** Only the owner can update their music
5. **Deleting Music:** Only the owner can delete their music

## Testing

The application includes comprehensive tests covering:
- User can become an artist
- Artist can upload music
- Non-artists cannot upload music
- Guest can view published music
- Guests cannot view draft music
- Owner can view their own draft music
- Non-owners cannot view others' draft music
- Artist can update their own music
- Artist cannot update others' music
- Artist can delete their own music
- Artist can view all their own music (drafts and published)

Run tests with:
```bash
php artisan test
```
