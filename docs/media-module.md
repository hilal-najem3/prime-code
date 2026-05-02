# 📦 Media Module — Full Documentation (Backend + Frontend)

---

# 🧠 Overview

The Media Module provides a **centralized system for file management** across the platform.

It supports:

- File upload (images, documents, etc.)
- Media attachment to models (polymorphic)
- Private & public storage
- Image variants (thumbnails, etc.)
- Usage tracking
- Secure access (private files)
- Full frontend integration (picker, preview, table, upload)

---

# 🏗️ Architecture

This module follows core platform rules:

- Modular structure (`Modules/Media`)
- Service-based logic (no controller logic)
- API standard response (`ApiResponse`)
- Policy-based authorization
- Frontend API abstraction layer

---

# ⚙️ Backend

---

## 📁 Structure

```
Modules/Media/
├── Models/
├── Controllers/
├── Services/
├── Requests/
├── Resources/
├── Policies/
├── Routes/
```

---

## 📦 Media Model

Core fields:

- disk (`public` / `private`)
- path
- filename
- mime_type
- size
- collection
- model_type / model_id (polymorphic)
- user_id

---

## 🔗 Relationships

```php
public function model()
{
    return $this->morphTo();
}
```

---

## 🔐 Private Media System

### Storage

```php
'disk' => 'private'
```

### URL generation

```php
media_url($media)
```

- Public → direct URL
- Private → `/media/{id}/secure`

---

## 🔒 Secure Access Flow

```
Frontend → URL
        → /media/{id}/secure
        → Controller
        → Policy check
        → Stream file
```

---

## 🧠 MediaService

### Functions

#### 1. List Media

```php
list(array $filters)
```

Supports:

- search
- collection
- pagination

---

#### 2. Find Media

```php
findOrFail(int $id)
```

---

#### 3. Usage

```php
usage(Media $media)
```

Returns where media is used.

---

#### 4. File Stream

```php
getFileStream(Media $media)
```

Used for private files.

---

## 🎯 Controller Rules

Controllers:

- MUST use services
- MUST use `ApiResponse`
- MUST NOT contain logic

Example:

```php
return ApiResponse::success(
    MediaResource::collection($media)
);
```

---

## 📤 Upload Flow

```
Request → Controller → Service
        → Store file
        → Save DB
        → Generate variants
        → Return MediaResource
```

---

## 🖼️ Image Variants

Generated via:

```php
ImageVariantService
```

Example:

```
image.jpg
image-thumb.jpg
image-medium.jpg
```

---

## 🛡️ Policies

Auto-registered per module.

Example:

```php
MediaPolicy::view($user, $media)
```

---

# 🌐 Frontend

---

## 📁 Structure

```
packages/media/
├── api/
├── components/
├── composables/
├── types/
├── utils/
```

---

## 🔌 API Layer

File: `media.api.ts`

### Methods

#### List

```ts
mediaApi.list({ page, search, collection });
```

---

#### Upload

```ts
mediaApi.upload(file, directory, collection, onProgress);
```

---

#### Attach

```ts
mediaApi.attach({
  media_id,
  model_type,
  model_id,
});
```

---

#### Delete

```ts
mediaApi.delete(mediaId);
```

---

#### Usage

```ts
mediaApi.usage(mediaId);
```

---

## 🧠 Composable

### `useMedia()`

Central state manager.

#### State

- items
- loading
- page / lastPage
- search
- collection

---

#### Methods

- `fetchMedia(reset)`
- `loadMore()`
- `upload()`
- `remove()`
- `getUsage()`

---

## 🧩 Components

---

### 1. MediaPicker

📄

Core UI for:

- selecting media
- uploading files
- drag & drop
- infinite scroll
- attach mode

#### Modes

| Mode   | Behavior          |
| ------ | ----------------- |
| select | returns IDs       |
| attach | attaches to model |

---

### 2. MediaField

Form component.

Used in forms:

```vue
<MediaField v-model="form.media" :multiple="true" collection="gallery" />
```

---

### 3. MediaPreviewModal

Displays:

- image preview
- metadata
- usage
- delete action

---

### 4. MediaSelect

Dropdown-based selection.

---

### 5. MediaLibraryPage

📄

Full media management page.

Supports:

- grid view
- table view (DataTable)
- upload
- search
- preview modal

---

## 📊 DataTable Integration

Uses remote mode:

```vue
<DataTable :data="items" :meta="meta" remote @change="fetchMedia" />
```

Aligned with API pagination

---

## 🧾 Types

```ts
interface Media {
  id: number;
  disk: string;
  path: string;
  filename: string;
  mime_type: string;
  url: string;
  variants?: Record<string, string>;
}
```

---

## 🔧 Utils

```ts
getMediaUrl(media, "thumb");
```

---

# 🔄 Full Flow Examples

---

## 📤 Upload + Attach

```
User selects file
→ MediaPicker
→ mediaApi.upload
→ backend stores file
→ variants generated
→ response returned
→ UI updates
```

---

## 🔗 Attach Existing Media

```
User selects existing file
→ mediaApi.attach
→ backend duplicates record
→ linked to model
```

---

## 🔒 Private Media Access

```
Frontend uses media.url
→ hits secure route
→ backend checks policy
→ streams file
```

---

# ⚠️ Rules & Best Practices

---

## Backend

- No logic in controllers
- Always use services
- Always use ApiResponse
- Always protect private media with policies

---

## Frontend

- Use API layer (no direct Axios)
- Use composables for state
- Keep components reusable
- Support localization

---

# 🚀 Future Upgrades

Planned improvements:

- Signed URLs (temporary access)
- Role-based media access
- Media folders system
- CDN support
- Advanced filters
- Bulk actions
- Permission-based UI hiding

---

# 🎯 Summary

This Media Module provides:

- Secure file storage
- Scalable architecture
- Full frontend integration
- SaaS-ready structure

It is designed to be:

```
modular
secure
extensible
production-ready
```

---

# 📌 Next Updates

This document will evolve with:

- advanced security
- performance optimizations
- UI improvements
- SaaS features

---
