"""
fixes python path so files can be imported from the parent folder.
"""
import os
os.sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
