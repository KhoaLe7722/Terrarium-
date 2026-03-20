# Bai tap web cay kieng

Project nay da duoc dong goi san bang Docker de nguoi khac clone ve la co the chay ca web va database mau.

## Chay nhanh

1. Cai `Docker Desktop`.
2. Clone repo.
3. Neu muon doi cong hoac mat khau database, copy `.env.example` thanh `.env` va sua gia tri trong do.
4. Chay:

```bash
docker compose up --build
```

Sau khi xong:

- Website: `http://localhost:8080`
- MySQL: `localhost:3307`
- Database: `terrarium_db`

## Database co san

Container MySQL se tu dong import:

- `data/terrarium_db.sql`
- `data/seed_demo_orders.sql`

Viec import chi xay ra o lan khoi dong dau tien khi volume `mysql_data` con rong.

Neu muon reset lai database tu dau:

```bash
docker compose down -v
docker compose up --build
```

## Tai khoan mau

- Admin: `admin@thuanphatgarden.vn`
- Mat khau: `Admin@123`

## Chay bang XAMPP cu

App van giu fallback ve:

- Host: `localhost`
- Port: `3306`
- User: `root`
- Password: rong
- Database: `terrarium_db`

Neu khong dat bien moi truong, project van tiep tuc chay theo kieu XAMPP nhu truoc.
