from reportlab.lib.pagesizes import letter
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.lib.units import inch
from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer, PageBreak, Image, KeepTogether
from reportlab.lib.enums import TA_CENTER, TA_LEFT, TA_JUSTIFY
from reportlab.lib import colors
import os

# Create PDF
pdf_file = 'c:/wamp64/www/checkout/Stuck_in_18th_Century.pdf'
doc = SimpleDocTemplate(pdf_file, pagesize=letter, topMargin=0.5*inch, bottomMargin=0.5*inch, leftMargin=0.75*inch, rightMargin=0.75*inch)

# Styles
styles = getSampleStyleSheet()
title_style = ParagraphStyle(
    'CustomTitle',
    parent=styles['Heading1'],
    fontSize=36,
    textColor=colors.HexColor('#1B3A5C'),
    spaceAfter=12,
    alignment=TA_CENTER,
    fontName='Helvetica-Bold'
)

heading_style = ParagraphStyle(
    'CustomHeading',
    parent=styles['Heading2'],
    fontSize=16,
    textColor=colors.HexColor('#2E86AB'),
    spaceAfter=10,
    spaceBefore=10,
    fontName='Helvetica-Bold'
)

body_style = ParagraphStyle(
    'CustomBody',
    parent=styles['BodyText'],
    fontSize=10,
    textColor=colors.HexColor('#2D3A4A'),
    spaceAfter=10,
    alignment=TA_JUSTIFY,
    leading=14
)

info_style = ParagraphStyle(
    'Info',
    parent=styles['Normal'],
    fontSize=10,
    textColor=colors.HexColor('#5A6E82'),
    alignment=TA_CENTER,
    spaceAfter=6
)

IMG_DIR = 'C:/tmp/pdf_images'

def add_img(name, width=3.5*inch, height=2.2*inch):
    path = os.path.join(IMG_DIR, name)
    if os.path.exists(path):
        try:
            return Image(path, width=width, height=height)
        except:
            return None
    return None

# Content
elements = []

# Title Page
elements.append(Spacer(1, 1.5*inch))
elements.append(Paragraph('Stuck in 18th Century', title_style))
elements.append(Spacer(1, 0.3*inch))
elements.append(Paragraph('Koli Akter', info_style))
elements.append(Paragraph('ID: 056', info_style))
elements.append(Paragraph('Course Code: ENG 3101', info_style))
elements.append(Paragraph('Introduction To ELT', info_style))
elements.append(PageBreak())

# Slide 2: Introduction
elements.append(Paragraph('Introduction', heading_style))
img = add_img('enlightenment.jpg')
if img:
    elements.append(img)
    elements.append(Spacer(1, 0.15*inch))
elements.append(Paragraph(
    'The 18th century is often called the Age of Enlightenment, a period when people began to rely more on science, logic, and reason rather than tradition or blind belief. It was a time of questioning—people started asking why things were the way they were and searched for answers through knowledge and observation.',
    body_style))
elements.append(PageBreak())

# Slide 3: Today's World
elements.append(Paragraph("Today's World", heading_style))
img = add_img('technology.jpg')
if img:
    elements.append(img)
    elements.append(Spacer(1, 0.15*inch))
elements.append(Paragraph(
    'Today, we live in a completely different world, shaped by digital technology, the internet, and advanced science. However, even with all this progress, a question still remains: are we truly different from people of the 18th century, or are we simply a modern version of them?',
    body_style))
elements.append(PageBreak())

# Slide 4: Surface Changes
elements.append(Paragraph('Surface Changes', heading_style))
img = add_img('digital_world.jpg')
if img:
    elements.append(img)
    elements.append(Spacer(1, 0.15*inch))
elements.append(Paragraph(
    'At first glance, it seems like we have changed a lot. Our world is faster, more connected, and more technologically advanced. We use smartphones, social media, artificial intelligence, and digital platforms in our daily lives. Information is available instantly, and communication happens across the globe within seconds. In this sense, we have clearly moved far beyond the 18th century.',
    body_style))
elements.append(PageBreak())

# Slide 5: Deeper Similarities
elements.append(Paragraph('But if we look deeper...', heading_style))
img = add_img('knowledge.jpg')
if img:
    elements.append(img)
    elements.append(Spacer(1, 0.15*inch))
elements.append(Paragraph(
    'But if we look deeper, the similarities become clearer. Like people of the Enlightenment, we strongly believe in science and rational thinking. We trust doctors, scientists, and technology. During global crises, such as pandemics, people turn to science for solutions.',
    body_style))
elements.append(PageBreak())

# Slide 6: Education & Knowledge
elements.append(Paragraph('Education & Knowledge', heading_style))
img = add_img('growth.jpg')
if img:
    elements.append(img)
    elements.append(Spacer(1, 0.15*inch))
elements.append(Paragraph(
    'Education is highly valued, just as it was during the Enlightenment. Students are encouraged to think critically, analyze information, and form logical arguments. This shows that the foundation built in the 18th century is still very much alive today.',
    body_style))
elements.append(PageBreak())

# Slide 7: Enlightenment & Social Competition
elements.append(Paragraph('The Other Side of Enlightenment', heading_style))
img = add_img('comparison.jpg')
if img:
    elements.append(img)
    elements.append(Spacer(1, 0.15*inch))
elements.append(Paragraph(
    'However, the Enlightenment period was not only about knowledge and reason. It was also a time of social competition, pride, and personal image. People cared deeply about how they were seen by others. Social status, fashion, and reputation played a huge role in everyday life. In many ways, society was built on appearance and public perception.',
    body_style))
elements.append(PageBreak())

# Slide 8: Modern Digital Presentation
elements.append(Paragraph('Digital Presentation Today', heading_style))
img = add_img('social_media.jpg')
if img:
    elements.append(img)
    elements.append(Spacer(1, 0.15*inch))
elements.append(Paragraph(
    'In the modern digital world, especially on platforms like Instagram, people present carefully designed versions of their lives. Photos are edited, moments are selected, and captions are crafted to create a certain image. People often show their happiest, most successful, or most attractive sides, while hiding their struggles and imperfections. This creates a gap between reality and representation.',
    body_style))
elements.append(PageBreak())

# Slide 9: The Perfect Day Illusion
elements.append(Paragraph('The Illusion of Perfection', heading_style))
img = add_img('perfection.jpg')
if img:
    elements.append(img)
    elements.append(Spacer(1, 0.15*inch))
elements.append(Paragraph(
    'For example, a person might post a picture of a perfect day out, smiling and looking confident. But what we do not see is the stress, the bad moments, or the ordinary parts of their life. This selective sharing creates an illusion, and others who see it may start comparing their own lives to this unrealistic standard.',
    body_style))
elements.append(PageBreak())

# Slide 10: Not New, Just Modern
elements.append(Paragraph('An Old Habit', heading_style))
img = add_img('identity.jpg')
if img:
    elements.append(img)
    elements.append(Spacer(1, 0.15*inch))
elements.append(Paragraph(
    'This behavior is not new—it is simply a modern version of an old habit. In the 18th century, people showed their status through clothing, manners, and social gatherings. Today, people do the same thing through posts, followers, and likes. The platform has changed, but the intention remains the same. Humans still want to be admired, respected, and noticed.',
    body_style))
elements.append(PageBreak())

# Slide 11: Validation
elements.append(Paragraph('The Issue of Validation', heading_style))
img = add_img('validation.jpg')
if img:
    elements.append(img)
    elements.append(Spacer(1, 0.15*inch))
elements.append(Paragraph(
    'Another important issue is validation. In the past, validation came from society—people wanted approval from their community or social circle. Today, validation comes in the form of likes, comments, and shares. A simple post can become a source of happiness or disappointment depending on how others react to it. This shows how deeply social approval still affects us.',
    body_style))
elements.append(PageBreak())

# Slide 12: Digital Pressure
elements.append(Paragraph('Amplified Pressure', heading_style))
img = add_img('online.jpg')
if img:
    elements.append(img)
    elements.append(Spacer(1, 0.15*inch))
elements.append(Paragraph(
    'At the same time, the digital world has made this pressure even stronger. In the 18th century, social comparison was limited to a small group of people. Now, through social media, we compare ourselves with hundreds or even thousands of others. This can create anxiety, low self-esteem, and a constant feeling of not being "good enough."',
    body_style))
elements.append(PageBreak())

# Slide 13: Misuse of Knowledge
elements.append(Paragraph('Misuse of Knowledge', heading_style))
img = add_img('misinformation.jpg')
if img:
    elements.append(img)
    elements.append(Spacer(1, 0.15*inch))
elements.append(Paragraph(
    'Even though we have access to more knowledge than ever before, we do not always use it wisely. The Enlightenment thinkers believed that reason would lead to progress and a better society. However, today we often see the opposite. Misinformation spreads quickly online, people follow trends without thinking, and sometimes emotions are valued more than facts.',
    body_style))
elements.append(PageBreak())

# Slide 14: Examples of Misinformation
elements.append(Paragraph('Following Without Thinking', heading_style))
img = add_img('communication.jpg')
if img:
    elements.append(img)
    elements.append(Spacer(1, 0.15*inch))
elements.append(Paragraph(
    'For example, many people share information on social media without checking if it is true. Others follow popular opinions just to fit in, rather than forming their own views. This shows that although we have the tools for critical thinking, we do not always apply them.',
    body_style))
elements.append(PageBreak())

# Slide 15: Technology & Identity
elements.append(Paragraph('Technology & Identity', heading_style))
img = add_img('change.jpg')
if img:
    elements.append(img)
    elements.append(Spacer(1, 0.15*inch))
elements.append(Paragraph(
    'Another interesting point is how technology affects our identity. In the digital world, people can create a completely different version of themselves. They can choose what to show and what to hide. Over time, this can make it difficult to separate real identity from online identity.',
    body_style))
elements.append(PageBreak())

# Slide 16: Self-Worth & Online Presence
elements.append(Paragraph('Self-Worth & Online Presence', heading_style))
img = add_img('selfworth.jpg')
if img:
    elements.append(img)
    elements.append(Spacer(1, 0.15*inch))
elements.append(Paragraph(
    'In some cases, people begin to measure their self-worth based on their online presence. This can be dangerous, especially for young people, who are still developing their sense of self. The need to appear perfect can lead to stress and dissatisfaction.',
    body_style))
elements.append(PageBreak())

# Slide 17: The Positive Side
elements.append(Paragraph('Positive Opportunities', heading_style))
img = add_img('connection.jpg')
if img:
    elements.append(img)
    elements.append(Spacer(1, 0.15*inch))
elements.append(Paragraph(
    'Despite all these similarities, it is important to recognize that there are also differences. Today, we have more opportunities for learning, expression, and connection. Social media can be used for education, awareness, and creativity. People can share ideas, support causes, and connect with others across cultures.',
    body_style))
elements.append(PageBreak())

# Slide 18: How We Use Technology
elements.append(Paragraph('The Problem Is Usage', heading_style))
img = add_img('balance.jpg')
if img:
    elements.append(img)
    elements.append(Spacer(1, 0.15*inch))
elements.append(Paragraph(
    'So, the problem is not technology itself, but how we use it. If we use technology wisely, we can continue the positive legacy of the Enlightenment—spreading knowledge, encouraging critical thinking, and improving society. But if we focus only on image, validation, and superficial success, then we are simply repeating the same patterns as the past.',
    body_style))
elements.append(PageBreak())

# Slide 19: Conclusion
elements.append(Paragraph('Conclusion', heading_style))
img = add_img('future.jpg')
if img:
    elements.append(img)
    elements.append(Spacer(1, 0.15*inch))
elements.append(Paragraph(
    'In conclusion, it is clear that we are not completely different from people of the 18th century. While our world has changed in terms of technology and science, human nature remains largely the same. Our desire for recognition, our concern for image, and our need for approval continue to shape our behavior. The only difference is that these qualities now exist in a digital form.',
    body_style))
elements.append(PageBreak())

# Slide 20: Final Thoughts
elements.append(Paragraph('Final Thoughts', heading_style))
img = add_img('progress.jpg')
if img:
    elements.append(img)
    elements.append(Spacer(1, 0.15*inch))
elements.append(Paragraph(
    'Therefore, it can be said that we are still "stuck in the 18th century"—not because we have failed to progress, but because we carry the same human tendencies into every new era. The challenge for us is not just to advance technologically, but to grow intellectually and emotionally as well.',
    body_style))
elements.append(PageBreak())

# Slide 21: Thank You
elements.append(Spacer(1, 2*inch))
elements.append(Paragraph('Thank You!', title_style))
elements.append(Spacer(1, 0.3*inch))
elements.append(Paragraph('Any Questions?', info_style))
elements.append(Spacer(1, 0.5*inch))
elements.append(Paragraph('Koli Akter  |  ID: 056  |  ENG 3101 - Introduction To ELT', info_style))

# Build PDF
doc.build(elements)
print(f'PDF saved: {pdf_file}')
print(f'Total pages: 21')
