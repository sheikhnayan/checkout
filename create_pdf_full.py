from reportlab.lib.pagesizes import letter
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.lib.units import inch
from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer, PageBreak, Table, TableStyle, Image
from reportlab.lib.enums import TA_CENTER, TA_JUSTIFY
from reportlab.lib import colors
import os

pdf_file = 'c:/wamp64/www/checkout/Stuck_in_18th_Century.pdf'
doc = SimpleDocTemplate(pdf_file, pagesize=letter, topMargin=0.35*inch, bottomMargin=0.35*inch, leftMargin=0.4*inch, rightMargin=0.4*inch)

styles = getSampleStyleSheet()

title_style = ParagraphStyle(
    'Title', parent=styles['Heading1'], fontSize=40, textColor=colors.HexColor('#1B3A5C'),
    spaceAfter=8, alignment=TA_CENTER, fontName='Helvetica-Bold'
)

heading_style = ParagraphStyle(
    'Heading', parent=styles['Heading2'], fontSize=15, textColor=colors.HexColor('#2E86AB'),
    spaceAfter=6, fontName='Helvetica-Bold'
)

body_style = ParagraphStyle(
    'Body', parent=styles['BodyText'], fontSize=9.5, textColor=colors.HexColor('#2D3A4A'),
    spaceAfter=6, alignment=TA_JUSTIFY, leading=13
)

info_style = ParagraphStyle(
    'Info', parent=styles['Normal'], fontSize=9, textColor=colors.HexColor('#5A6E82'),
    alignment=TA_CENTER, spaceAfter=3
)

IMG_DIR = 'C:/tmp/pdf_images_new'

def get_img(name, w=2.2*inch, h=3*inch):
    path = f'{IMG_DIR}/{name}'
    if os.path.exists(path):
        try:
            return Image(path, width=w, height=h)
        except:
            return None
    return None

elements = []

# PAGE 1: Title
elements.append(Spacer(1, 1.8*inch))
elements.append(Paragraph('Stuck in 18th Century', title_style))
elements.append(Spacer(1, 0.35*inch))
elements.append(Paragraph('Koli Akter', info_style))
elements.append(Paragraph('ID: 056', info_style))
elements.append(Paragraph('Course Code: ENG 3101', info_style))
elements.append(Paragraph('Introduction To ELT', info_style))
elements.append(PageBreak())

# Helper function to create page with image
def create_page_with_image(heading, text, img_name):
    img = get_img(img_name)
    content = []
    content.append(Paragraph(heading, heading_style))
    content.append(Paragraph(text, body_style))

    if img:
        row = [[Spacer(1, 0.05*inch)], [img]]
        table = Table(row, colWidths=[2.2*inch])
        table.setStyle(TableStyle([('ALIGN', (0, 0), (-1, -1), 'CENTER'), ('VALIGN', (0, 0), (-1, -1), 'TOP')]))
        content.append(table)

    # Create main layout: text on left, image on right
    main_row = [content]
    if img:
        main_row = [content, [img]]
        main_table = Table([main_row], colWidths=[3.7*inch, 2.2*inch])
    else:
        main_table = Table([main_row], colWidths=[6*inch])

    main_table.setStyle(TableStyle([
        ('ALIGN', (0, 0), (-1, -1), 'LEFT'),
        ('VALIGN', (0, 0), (-1, -1), 'TOP'),
        ('RIGHTPADDING', (0, 0), (0, 0), 0.15*inch) if img else ('RIGHTPADDING', (0, 0), (0, 0), 0),
    ]))

    elements.append(main_table)
    elements.append(PageBreak())

# PAGE 2: Introduction
create_page_with_image(
    'Introduction',
    'The 18th century is often called the Age of Enlightenment, a period when people began to rely more on science, logic, and reason rather than tradition or blind belief. It was a time of questioning—people started asking why things were the way they were and searched for answers through knowledge and observation. This era marked a shift in human thought, where individuals began to challenge established norms and seek rational explanations for the world around them. The impact of this period continues to shape how we approach knowledge and understanding today.',
    'page2_enlightenment.jpg'
)

# PAGE 3: Today's World
create_page_with_image(
    "Today's World",
    "Today, we live in a completely different world, shaped by digital technology, the internet, and advanced science. However, even with all this progress, a question still remains: are we truly different from people of the 18th century, or are we simply a modern version of them? Our lives are intertwined with technology in ways our ancestors could never have imagined. Yet beneath the surface, many of our behaviors and motivations remain fundamentally unchanged. The paradox of modern life is that while we have transformed our tools and environment, we have not fundamentally transformed ourselves.",
    'page3_today.jpg'
)

# PAGE 4: Surface Changes
create_page_with_image(
    'Surface Changes',
    'At first glance, it seems like we have changed a lot. Our world is faster, more connected, and more technologically advanced. We use smartphones, social media, artificial intelligence, and digital platforms in our daily lives. Information is available instantly, and communication happens across the globe within seconds. In this sense, we have clearly moved far beyond the 18th century. The speed at which we can share information, conduct business, and maintain relationships would astonish an 18th-century person. Yet these surface-level changes mask deeper patterns that have persisted throughout history.',
    'page4_changes.jpg'
)

# PAGE 5: Deeper Similarities
create_page_with_image(
    'But if we look deeper...',
    'But if we look deeper, the similarities become clearer. Like people of the Enlightenment, we strongly believe in science and rational thinking. We trust doctors, scientists, and technology. During global crises, such as pandemics, people turn to science for solutions. This demonstrates that our faith in rational thought and scientific inquiry remains as strong today as it was three centuries ago. We still rely on evidence, experimentation, and logical reasoning to solve our problems. The scientific method continues to be our most trusted tool for understanding the world, proving that the fundamental values of the Enlightenment are still embedded in our modern consciousness.',
    'page5_science.jpg'
)

# PAGE 6: Education
create_page_with_image(
    'Education & Knowledge',
    'Education is highly valued, just as it was during the Enlightenment. Students are encouraged to think critically, analyze information, and form logical arguments. This shows that the foundation built in the 18th century is still very much alive today. Our educational systems emphasize the importance of developing critical thinking skills, the ability to question assumptions, and the capacity to engage in reasoned debate. Universities continue to serve as centers for intellectual inquiry and advancement of knowledge. The Enlightenment vision of an educated citizenry capable of rational thought remains our ideal. Parents, educators, and societies continue to invest heavily in education because we recognize its transformative power in shaping both individuals and societies.',
    'page6_education.jpg'
)

# PAGE 7: Social Competition
create_page_with_image(
    'The Other Side of Enlightenment',
    'However, the Enlightenment period was not only about knowledge and reason. It was also a time of social competition, pride, and personal image. People cared deeply about how they were seen by others. Social status, fashion, and reputation played a huge role in everyday life. In many ways, society was built on appearance and public perception. The aristocracy displayed their wealth through lavish clothing, grand estates, and elaborate social ceremonies. Merchants strived to climb the social ladder through visible displays of success. Even those of modest means tried to present themselves as best as they could. This obsession with social standing and public image created a society where what others thought of you was paramount to your sense of identity and worth.',
    'page7_social.jpg'
)

# PAGE 8: Digital Presentation
create_page_with_image(
    'Digital Presentation Today',
    'In the modern digital world, especially on platforms like Instagram, people present carefully designed versions of their lives. Photos are edited, moments are selected, and captions are crafted to create a certain image. People often show their happiest, most successful, or most attractive sides, while hiding their struggles and imperfections. This creates a gap between reality and representation. The tools have changed, but the underlying impulse remains the same. Instead of carefully selecting what to wear to a social gathering, people now carefully select which photos to post. Instead of practicing witty remarks for dinner parties, people now craft the perfect captions. The medium is new, but the goal is identical: to present the best possible version of ourselves to the world.',
    'page8_instagram.jpg'
)

# PAGE 9: Illusion
create_page_with_image(
    'The Illusion of Perfection',
    'For example, a person might post a picture of a perfect day out, smiling and looking confident. But what we do not see is the stress, the bad moments, or the ordinary parts of their life. This selective sharing creates an illusion, and others who see it may start comparing their own lives to this unrealistic standard. The person behind the photo may have had an argument with a loved one, financial worries, or health concerns—but none of that appears in the curated image. Viewers, seeing only the highlight reel, begin to feel inadequate about their own lives. This creates a vicious cycle where everyone is presenting an unrealistic version of their life, and everyone is comparing themselves to these false images. The result is widespread dissatisfaction.',
    'page9_perfection.jpg'
)

# PAGE 10: Old Habit
create_page_with_image(
    'An Old Habit',
    'This behavior is not new—it is simply a modern version of an old habit. In the 18th century, people showed their status through clothing, manners, and social gatherings. Today, people do the same thing through posts, followers, and likes. The platform has changed, but the intention remains the same. Humans still want to be admired, respected, and noticed. A wealthy merchant in the 1700s would wear fine silks and host elaborate parties to demonstrate their success. Today, someone achieves the same goal by posting photos of luxury vacations and expensive purchases. The wealthy in the 18th century displayed their libraries through scholarly discussion; today they display intellectual pursuits through curated posts. Whether through material possessions, social gatherings, or digital platforms, the fundamental human desire to signal status remains constant.',
    'page10_habit.jpg'
)

# PAGE 11: Validation (no image)
elements.append(Paragraph('The Issue of Validation', heading_style))
elements.append(Paragraph(
    'Another important issue is validation. In the past, validation came from society—people wanted approval from their community or social circle. Today, validation comes in the form of likes, comments, and shares. A simple post can become a source of happiness or disappointment depending on how others react to it. This shows how deeply social approval still affects us. A young woman posts a photo, and her mood for the entire day may depend on how many likes it receives. A professional shares a career achievement, and the number of congratulatory comments becomes a measure of their success. We have created a system where our self-worth is constantly being measured and quantified by others responses. This digitized validation system is more immediate, more public, and potentially more addictive than anything that existed in previous centuries. The psychological impact of this constant feedback loop is only beginning to be understood, but early research suggests it can contribute to anxiety, depression, and a fragile sense of self-worth.',
    body_style
))
elements.append(PageBreak())

# PAGE 12: Pressure
create_page_with_image(
    'Amplified Pressure',
    'At the same time, the digital world has made this pressure even stronger. In the 18th century, social comparison was limited to a small group of people. Now, through social media, we compare ourselves with hundreds or even thousands of others. This can create anxiety, low self-esteem, and a constant feeling of not being adequate. The scope of social comparison has expanded exponentially. A teenager in a small town can now compare themselves to beautiful people from around the world. A professional can measure their career success against countless peers. This constant, inescapable comparison creates a perpetual state of inadequacy. There will always be someone smarter, more attractive, more successful, or more talented. In a pre-digital world, your comparison group was limited; today, it is essentially infinite. This psychological burden is unprecedented in human history.',
    'page12_pressure.jpg'
)

# PAGE 13: Misinformation
create_page_with_image(
    'Misuse of Knowledge',
    'Even though we have access to more knowledge than ever before, we do not always use it wisely. The Enlightenment thinkers believed that reason would lead to progress and a better society. However, today we often see the opposite. Misinformation spreads quickly online, people follow trends without thinking, and sometimes emotions are valued more than facts. We have access to more information than any generation in history, yet we may be less informed. The ability to find accurate information is hampered by algorithms that prioritize engagement over truth. Misinformation can spread globally in minutes, while corrections take weeks to gain traction. Social media platforms have become powerful tools for spreading falsehoods and conspiracy theories. What the Enlightenment thinkers could not have anticipated is that having access to knowledge is not the same as having wisdom.',
    'page13_misinformation.jpg'
)

# PAGE 14: Following Without Thinking
create_page_with_image(
    'Following Without Thinking',
    'For example, many people share information on social media without checking if it is true. Others follow popular opinions just to fit in, rather than forming their own views. This shows that although we have the tools for critical thinking, we do not always apply them. It is easier to share information that confirms what we already believe than to do the research necessary to verify it. It is more socially convenient to adopt popular opinions than to stand out with unpopular ones. We have not evolved emotionally to handle the information age we inhabit. Our brains still operate according to ancient patterns: we are social creatures who value belonging. When millions of people online seem to believe something, our instinct is often to go along rather than investigate independently. The result is a fascinating paradox: we have unprecedented access to information, but use it primarily to reinforce what we already think.',
    'page14_thinking.jpg'
)

# PAGE 15: Identity
create_page_with_image(
    'Technology & Identity',
    'Another interesting point is how technology affects our identity. In the digital world, people can create a completely different version of themselves. They can choose what to show and what to hide. Over time, this can make it difficult to separate real identity from online identity. Some people develop different personas for different platforms. They might be professional on LinkedIn, casual on Facebook, and edgy on Twitter. In multiplayer online games, people adopt entirely different identities. This ability to create and control our identity is unprecedented. Historically, people had little choice about their identity; it was largely determined by birth and social class. Now, we have the power to reinvent ourselves constantly. But this freedom comes with a cost: the risk of losing touch with our authentic self. Some people become so invested in online personas that they forget who they really are.',
    'page15_identity.jpg'
)

# PAGE 16: Self-Worth
create_page_with_image(
    'Self-Worth & Online Presence',
    'In some cases, people begin to measure their self-worth based on their online presence. This can be dangerous, especially for young people, who are still developing their sense of self. The need to appear perfect can lead to stress and dissatisfaction. Teenagers report anxiety when their content does not get immediate engagement. Young adults feel depressed when comparing their lives to carefully curated versions online. Adults worry about professional reputation based on social media. The metric for self-worth has shifted from internal standards to external validation metrics. For people whose sense of self is still forming, the impact can be profound. A teenager whose identity is developing may internalize that their worth depends on presenting an attractive image online. They may develop eating disorders or suffer from depression when failing to achieve desired engagement. The long-term psychological impact of growing up where identity is quantified is still unknown.',
    'page16_selfworth.jpg'
)

# PAGE 17: Positive
create_page_with_image(
    'Positive Opportunities',
    'Despite all these similarities, it is important to recognize that there are also differences. Today, we have more opportunities for learning, expression, and connection. Social media can be used for education, awareness, and creativity. People can share ideas, support causes, and connect with others across cultures. A person can learn almost anything through online courses. Artists can find audiences they could never have reached before. Activists can organize movements that would have taken years to coordinate without technology. Scientists collaborate across continents in real time. Marginalized communities can find support and solidarity online. Social media has enabled movements for environmental awareness and social justice. Educational content is available free to anyone with an internet connection. People can maintain relationships across vast distances. The tools themselves are neutral; what matters is how we use them.',
    'page17_connection.jpg'
)

# PAGE 18: Usage
create_page_with_image(
    'The Problem Is Usage',
    'So, the problem is not technology itself, but how we use it. If we use technology wisely, we can continue the positive legacy of the Enlightenment—spreading knowledge, encouraging critical thinking, and improving society. But if we focus only on image, validation, and superficial success, then we are simply repeating the same patterns as the past. We have a choice. We can use technology to broadcast only achievements and hide struggles, or we can tell more honest stories. We can absorb misinformation passively, or use it as a tool for rigorous research. We can measure worth by external metrics, or develop internal standards. We can follow trends mindlessly, or think critically about truth and value. The Enlightenment was ultimately about human potential—the capacity of individuals to reason, improve themselves, and contribute to a better world. We have not lost that capacity. The tools have changed, but our ability to think, choose, and act remains. The question is whether we will exercise it.',
    'page18_choice.jpg'
)

# PAGE 19: Conclusion (no image)
elements.append(Paragraph('Conclusion', heading_style))
elements.append(Paragraph(
    'In conclusion, it is clear that we are not completely different from people of the 18th century. While our world has changed in terms of technology and science, human nature remains largely the same. Our desire for recognition, our concern for image, and our need for approval continue to shape our behavior. The only difference is that these qualities now exist in a digital form. The tools have become more sophisticated, but the underlying impulses are ancient. We still seek status, still desire to be admired, still want to belong. We still struggle between individual desires and social expectations. We still grapple with questions of identity and self-worth. The Enlightenment was not just about advancement of knowledge; it was also a period of human folly, social competition, and pursuit of status. We inherited both aspects of that legacy. We have inherited the capacity for reason and critical thinking, belief in progress through education, and faith in human potential. But we have also inherited vanity, competitiveness, and obsession with social standing. What makes the modern era different is not that we have transcended these human qualities, but that we have created new platforms for their expression.',
    body_style
))
elements.append(PageBreak())

# PAGE 20: Final Thoughts (no image)
elements.append(Paragraph('Final Thoughts', heading_style))
elements.append(Paragraph(
    'Therefore, it can be said that we are still stuck in the 18th century—not because we have failed to progress, but because we carry the same human tendencies into every new era. The challenge for us is not just to advance technologically, but to grow intellectually and emotionally as well. We need to recognize these patterns within ourselves and make conscious choices about how we use technology and present ourselves to the world. We need wisdom to match our access to information. We need to cultivate self-awareness about our motivations for sharing and validating ourselves through digital platforms. We need to remember that the Enlightenment was ultimately about potential for human improvement. We have the same potential that our ancestors had, but we have better tools. The question is whether we will use these tools to improve ourselves and our world, or simply become more efficient at repeating the same mistakes. The 21st century is still young. We are not helpless prisoners of technology or human nature. We have agency. We have the ability to choose.',
    body_style
))
elements.append(PageBreak())

# PAGE 21: Thank You
elements.append(Spacer(1, 2*inch))
elements.append(Paragraph('Thank You!', title_style))
elements.append(Spacer(1, 0.3*inch))
elements.append(Paragraph('Any Questions?', info_style))
elements.append(Spacer(1, 0.5*inch))
elements.append(Paragraph('Koli Akter  |  ID: 056  |  ENG 3101 - Introduction To ELT', info_style))

doc.build(elements)
print('PDF created successfully: Stuck_in_18th_Century.pdf')
