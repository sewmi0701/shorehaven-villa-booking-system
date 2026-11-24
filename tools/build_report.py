#!/usr/bin/env python3
"""Regenerate a four-page report draft; after running site, replace screenshots in report/screenshots."""
from pathlib import Path
import io, sys
from reportlab.pdfgen import canvas
from reportlab.lib.pagesizes import A4
from reportlab.lib.colors import HexColor, Color
from reportlab.lib.utils import ImageReader
from reportlab.pdfbase import pdfmetrics
from reportlab.pdfbase.ttfonts import TTFont
from reportlab.platypus import Paragraph
from reportlab.lib.styles import ParagraphStyle
from reportlab.lib.enums import TA_LEFT
try: import cairosvg
except ImportError: cairosvg=None

BASE=Path(__file__).resolve().parent.parent
OUTPUT=BASE/'project_report.pdf'
F='/usr/share/fonts/truetype/lato'
pdfmetrics.registerFont(TTFont('Lato',f'{F}/Lato-Regular.ttf'))
pdfmetrics.registerFont(TTFont('Lato-Bold',f'{F}/Lato-Bold.ttf'))
pdfmetrics.registerFont(TTFont('Lato-Italic',f'{F}/Lato-Italic.ttf'))
W,H=A4
INK=HexColor('#163b40'); SEA=HexColor('#215e63'); CREAM=HexColor('#f6f7f1')
SAND=HexColor('#f0e5d3'); GOLD=HexColor('#bc815c'); MUTED=HexColor('#64777a')

def text(c,x,y,t,size=10,color=INK,bold=False):
    c.setFillColor(color);c.setFont('Lato-Bold' if bold else 'Lato',size);c.drawString(x,y,t)
def para(c,x,top,txt,width,size=10,leading=15,color=MUTED,bold=False):
    style=ParagraphStyle('body',fontName='Lato-Bold' if bold else 'Lato',fontSize=size,leading=leading,textColor=color,spaceAfter=0)
    p=Paragraph(txt,style)
    tw,th=p.wrap(width,700)
    p.drawOn(c,x,top-th)
    return top-th
def rect(c,x,y,w,h,color):c.setFillColor(color);c.rect(x,y,w,h,fill=1,stroke=0)
def base(c,num,section):
    rect(c,0,H-52,W,52,INK)
    text(c,40,H-32,'✺  SHOREHAVEN VILLA',11,HexColor('#f9f5e8'),True)
    c.setFont('Lato',9);c.setFillColor(HexColor('#c3d2cf'));c.drawRightString(W-40,H-32,section.upper())
    rect(c,40,44,W-80,1,HexColor('#dce5dd'))
    text(c,40,27,'IT1208 · WEB TECHNOLOGIES · 2026',8,MUTED)
    c.setFont('Lato',8);c.setFillColor(MUTED);c.drawRightString(W-40,27,f'{num} / 4  ·  DRAFT')
def heading(c,x,top,kicker,title):
    text(c,x,top,kicker.upper(),9,GOLD,True)
    text(c,x,top-45,title,27,INK,True)
    return top-70

def svg_img(c,filename,x,y,w,h):
    if not cairosvg:return
    path=BASE/'assets/images'/filename
    data=cairosvg.svg2png(url=str(path),output_width=1000)
    c.drawImage(ImageReader(io.BytesIO(data)),x,y,width=w,height=h,mask='auto',preserveAspectRatio=False)

def screenshot_or_art(c,filename,svg,x,y,w,h):
    path=BASE/'report/screenshots'/filename
    if path.exists():
        c.drawImage(str(path),x,y,width=w,height=h,preserveAspectRatio=True,anchor='c',mask='auto')
        return True
    svg_img(c,svg,x,y,w,h)
    return False

c=canvas.Canvas(str(OUTPUT),pagesize=A4)
c.setTitle('Shorehaven Villa — Coastal Room Booking System — Report Draft')
# ---- Page 1 ----
rect(c,0,H-315,W,315,INK)
rect(c,34,H-293,5,205,GOLD)
text(c,58,H-104,'IT1208  /  CONTINUOUS ASSESSMENT 02',10,HexColor('#e5c6aa'),True)
text(c,55,H-159,'SHOREHAVEN',43,HexColor('#fff9ed'),True)
text(c,58,H-204,'COASTAL VILLA',29,HexColor('#fff9ed'))
text(c,58,H-243,'Dynamic Web-Based Room Booking System',12,HexColor('#cfe1de'))
text(c,58,H-274,'MIRISSA, SRI LANKA  ·  2026',9,HexColor('#d8b99b'),True)
text(c,40,H-352,'PROJECT REPORT  /  DRAFT TO CUSTOMIZE',11,GOLD,True)
text(c,40,H-384,'Student name: ____________________________',10,INK)
text(c,323,H-384,'Student ID: ___________________',10,INK)
rect(c,40,H-415,W-80,1,HexColor('#dee9e1'))
y=heading(c,40,H-435,'01 / Project overview','A quieter way to book the coast')
y=para(c,40,y,'Shorehaven Villa is a fictional coastal property in Mirissa. This application allows visitors to browse accommodation, search by date and occupancy, create accounts and submit booking requests. A separate administrator dashboard manages rooms and reservation decisions.',W-80,size=10,leading=16)
y-=14
text(c,40,y,'CORE OBJECTIVES',9,GOLD,True);y-=19
for line in ['Responsive interface built with HTML5, CSS3 and JavaScript.','PHP sessions for registration and authentication, with MySQL persistence.','Server-validated booking totals and conflict-free confirmed reservations.','Room management and booking approval/cancellation for administrators.']:
    y=para(c,56,y,'<b>•</b> '+line,W-97,9.5,14)-2
y-=6
text(c,40,y,'TECH STACK',9,GOLD,True);y-=20
for x,heading_txt,body_txt in [(40,'FRONT END','HTML5 · CSS3 · Vanilla JS'),(222,'BACK END','PHP 8 · Sessions · PDO'),(404,'DATA','MySQL · InnoDB · SQL')]:
    rect(c,x,y-42,168,53,CREAM)
    text(c,x+12,y-7,heading_txt,9,SEA,True)
    text(c,x+12,y-25,body_txt,8.1,MUTED)
rect(c,40,73,W-80,46,SAND)
para(c,51,110,'<b>Important:</b> This is a draft generated from the application implementation. Add your personal details, take real screenshots after local deployment, and record actual test outcomes before submission.',W-105,8.2,12,INK)
c.showPage()
# ---- Page 2 ----
base(c,2,'Interface design')
y=heading(c,40,H-85,'02 / Front-end walkthrough','Designed for relaxed discovery')
y=para(c,40,y,'The experience uses a neutral coastal palette, large room illustrations, responsive grids and clear calls to action. The following illustrations show the visual style; <b>replace them with screenshots from the running PHP pages for final submission.</b>',W-80,9,14)-16
text(c,40,y,'HOME PAGE / COASTAL HERO',9,GOLD,True);y-=15
used=screenshot_or_art(c,'home.png','villa.svg',40,y-169,W-80,167)
y-=188
text(c,40,y,'ROOMS / GALLERY VISUALS',9,GOLD,True);y-=13
pW=(W-96)/3
for idx,(image,art) in enumerate([('rooms.png','ocean.svg'),('booking.png','palm.svg'),('admin.png','garden.svg')]):
    x=40+idx*(pW+8)
    screenshot_or_art(c,image,art,x,y-117,pW,115)
y-=131
labels=[('Room gallery','Room search and filters'),('Booking form','Guest details and costs'),('Admin dashboard','Room / booking management')]
for idx,(a,b) in enumerate(labels):
    x=40+idx*(pW+8)
    text(c,x,y,a,9,INK,True)
    text(c,x,y-15,b,8,MUTED)
y-=48
rect(c,40,y-109,W-80,110,CREAM)
text(c,54,y-22,'REQUIRED SCREENSHOTS TO INSERT AFTER RUNNING THE APP',9,SEA,True)
for j,a in enumerate(['01  Home & room search','02  Rooms + filtering','03  Guest booking form','04  My Bookings + statuses','05  Admin dashboard','06  Admin management']):
    col=j%2;row=j//2
    text(c,54+col*257,y-42-row*20,a+' — screenshot',7.8,INK)
c.showPage()
# ---- Page 3 ----
base(c,3,'System architecture')
y=heading(c,40,H-85,'03 / Data & server-side flow','Built on connected records')
y=para(c,40,y,'MySQL uses three InnoDB tables with two foreign-key relationships. The guests own bookings, while each booking references the selected room. The extra contact fields preserve guest-specific booking details.',W-80,9,14)-18
# ER diagram boxes
boxes=[(40,'USERS',[('PK','user_id'),('','name / email / phone'),('','password hash'),('','role')]),(228,'BOOKINGS',[('PK','booking_id'),('FK','user_id / room_id'),('','check_in / check_out'),('','guests / total_amount'),('','status / guest contact')]),(416,'ROOMS',[('PK','room_id'),('','name / type'),('','price / capacity'),('','facilities / status')])]
boxwidth=141
for x,tag,fields in boxes:
    rect(c,x,y-134,boxwidth,135,CREAM)
    rect(c,x,y-24,boxwidth,25,SEA)
    text(c,x+10,y-16,tag,10,HexColor('#fffef5'),True)
    for i,(key,val) in enumerate(fields):
        if key: text(c,x+8,y-45-i*19,key,7,GOLD,True)
        text(c,x+(25 if key else 8),y-45-i*19,val,7.9,INK)
# connectors
c.setStrokeColor(GOLD);c.setLineWidth(1.8)
c.line(181,y-68,228,y-68);c.line(369,y-68,416,y-68)
text(c,185,y-59,'1:N',7,GOLD,True);text(c,374,y-59,'N:1',7,GOLD,True)
y-=166
text(c,40,y,'KEY WORKFLOWS',9,GOLD,True);y-=24
flows=[
('01','SEARCH','Visitor supplies dates and guests; SQL excludes rooms with overlapping confirmed stays.'),
('02','BOOK','Guest signs in; PHP revalidates dates, capacity and stored room price; request is pending.'),
('03','REVIEW','Admin confirms only if no existing confirmed stay overlaps; otherwise request remains pending.'),
('04','MANAGE','Guests can cancel their pending requests; admin can cancel confirmed reservations.'),
]
for number,title,description in flows:
    rect(c,40,y-33,35,35,SEA)
    text(c,49,y-23,number,11,HexColor('#ffffff'),True)
    text(c,89,y-8,title,9,INK,True)
    para(c,89,y-13,description,W-135,8.6,12)
    y-=62
y-=6
rect(c,40,y-76,W-80,79,SAND)
text(c,53,y-16,'SECURITY & INTEGRITY',9,SEA,True)
para(c,53,y-23,'Prepared PDO statements prevent SQL injection. Passwords use password_hash / password_verify. CSRF tokens protect POST actions; role checks restrict administrative paths; escaping protects displayed text. PHP recalculates totals and uses transactions with room-row locks during booking decisions.',W-107,8.7,13,INK)
c.showPage()
# ---- Page 4 ----
base(c,4,'Validation and reflection')
y=heading(c,40,H-85,'04 / Evaluation & next steps','Testing, learning, improving')
y=para(c,40,y,'The checks below are <b>planned manual tests</b>, not claims of completed live testing. Full test instructions are in tests/TEST_PLAN.md. Replace statuses with real outcomes after running the system on XAMPP/MySQL.',W-80,9,14)-14
x=40;total=W-80
rect(c,x,y-25,total,26,SEA)
for xpos,txt in [(x+12,'TEST CASE'),(x+234,'EXPECTED RESULT'),(x+433,'STATUS')]:text(c,xpos,y-17,txt,8,HexColor('#ffffff'),True)
y-=25
rows=[('Register guest','User saved; duplicate email blocked'),('Login / session','Guest and admin role enforced'),('Search unavailable dates','Confirmed conflicts excluded'),('Booking total','Server price x night count'),('Guest cancellation','Only own pending request cancelled'),('Admin confirmation','Overlapping confirmation refused'),('Room management','Create / edit / deactivate works'),('Responsive layouts','Mobile and desktop usable')]
for i,(case,expected) in enumerate(rows):
    if i%2==0:rect(c,x,y-34,total,34,CREAM)
    text(c,x+11,y-22,case,8.5,INK)
    text(c,x+234,y-22,expected,7.8,MUTED)
    text(c,x+433,y-22,'TO TEST',7.7,GOLD,True)
    y-=34
y-=33
text(c,40,y,'CHALLENGES AND LESSONS LEARNED',10,GOLD,True);y-=22
for a,b in [
('Preventing double bookings','Use the strict overlap predicate and transaction locks rather than relying on front-end checks.'),
('Protecting guest data','Store hashed passwords, apply access control, and filter bookings by session user ID.'),
('Maintaining responsive UI','Test forms, filtering layouts and navigation at multiple device widths.'),
('Keeping source maintainable','Reuse shared PHP layouts, helper functions and database methods.')]:
    text(c,40,y,a,9,INK,True)
    y=para(c,40,y-7,b,W-90,8.5,12)-19
y-=5
rect(c,40,y-73,W-80,80,SAND)
text(c,54,y-13,'CONCLUSION',9,SEA,True)
para(c,54,y-20,'The project illustrates an end-to-end request-and-confirm reservation workflow with relational data and separate guest/admin privileges. Before submission, run all tests, add authentic page screenshots, record findings and explain the implementation in your own words.',W-106,8.5,13,INK)
c.showPage();c.save()
print(f'Created {OUTPUT} ({OUTPUT.stat().st_size} bytes)')
