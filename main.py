import os
from contextlib import asynccontextmanager
from pathlib import Path
from fastapi import FastAPI
from fastapi.staticfiles import StaticFiles
from starlette.middleware.sessions import SessionMiddleware
from apscheduler.schedulers.background import BackgroundScheduler

from app.config import settings
from app.database import engine, Base
from app.routes.web import router as web_router
from app.routes.admin import router as admin_router
from app.routes.api import router as api_router
from app.poller import run_poll_all
from seed import seed_database

# Scheduler for periodic background polling
scheduler = BackgroundScheduler()

@asynccontextmanager
async def lifespan(app: FastAPI):
    # 1. Ensure DB tables and initial seeds exist
    Base.metadata.create_all(bind=engine)
    seed_database()

    # 2. Start Background Poller
    scheduler.add_job(
        run_poll_all,
        "interval",
        seconds=settings.POLL_INTERVAL_SECONDS,
        id="network_poller",
        replace_existing=True
    )
    scheduler.start()
    print(f"[{settings.APP_NAME}] Background poller started (interval: {settings.POLL_INTERVAL_SECONDS}s).")

    yield

    # Shutdown
    if scheduler.running:
        scheduler.shutdown()
        print(f"[{settings.APP_NAME}] Background poller stopped.")


app = FastAPI(
    title=settings.APP_NAME,
    version="2.0.0",
    docs_url="/docs" if settings.DEBUG else None,
    redoc_url=None,
    lifespan=lifespan
)

# Secure Session Middleware (HttpOnly signed session cookie)
app.add_middleware(
    SessionMiddleware,
    secret_key=settings.SECRET_KEY,
    session_cookie="readiness_session",
    max_age=86400 * 7,  # 7 days
    same_site="lax",
    https_only=not settings.DEBUG
)

# Mount Static Files
static_dir = Path(__file__).resolve().parent / "app" / "static"
app.mount("/static", StaticFiles(directory=str(static_dir)), name="static")

# Register Routers
app.include_router(web_router)
app.include_router(admin_router)
app.include_router(api_router)

if __name__ == "__main__":
    import uvicorn
    print(f"Starting {settings.APP_NAME} on http://{settings.HOST}:{settings.PORT}")
    uvicorn.run("main:app", host=settings.HOST, port=settings.PORT, reload=settings.DEBUG)
