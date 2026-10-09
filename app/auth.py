from typing import Optional
from starlette.requests import Request
from starlette.responses import RedirectResponse
from sqlalchemy.orm import Session
from app.models import Member

def get_current_user(request: Request, db: Session) -> Optional[Member]:
    user_id = request.session.get("user_id")
    if not user_id:
        return None
    return db.query(Member).filter(Member.id == user_id).first()

def require_login(request: Request, db: Session) -> Optional[Member]:
    user = get_current_user(request, db)
    return user

def require_admin(request: Request, db: Session) -> Optional[Member]:
    user = get_current_user(request, db)
    if not user or user.status != "ADMIN":
        return None
    return user

def set_flash(request: Request, message: str, category: str = "info"):
    request.session["flash"] = {"message": message, "category": category}

def get_flash(request: Request) -> Optional[dict]:
    return request.session.pop("flash", None)
